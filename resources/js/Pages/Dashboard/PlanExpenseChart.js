import React, {useEffect, useState} from 'react';
import {usePage} from "@inertiajs/inertia-react";
import {PieChart, Pie, Tooltip, ResponsiveContainer, Cell} from 'recharts';
import {Alert} from "@material-ui/lab";
import AutocompleteSelect from "@/Components/AutocompleteSelect";
import Table from "@/Components/Table";
import Button from "@/Components/Button";

export default function PlanExpenseChart() {
    const { createdPlans, memberPlans } = usePage().props;
    const [ planExpenditutres, setPlanExpenditutres ] = useState([]);
    const [ plansList, setPlansList ] = useState([]);
    const [ chosenPlansForViewing, setChosenPlansForViewing ] = useState([]);
    const [ selectedPlan, setSelectedPlan ] = useState();
    const [ selectedPlanError, setSelectedPlanError ] = useState('');

    useEffect(() => {
        setPlansList([
            ...createdPlans,
            ...memberPlans,
        ]);
    }, []);

    useEffect(() => {
        if (plansList.length > 0) {
            setSelectedPlan(plansList[0]);
        }
    }, [plansList]);

    const COLORS = ['#0088FE', '#00C49F', '#FFBB28', '#FF8042', '#911eb4', '#42d4f4', '#808000', '#469990', '#aaffc3', '#dcbeff'];
    const data01 = [
        { name: 'Group A', value: 400 },
        { name: 'Group B', value: 300 },
        { name: 'Group C', value: 300 },
        { name: 'Group D', value: 200 },
        { name: 'Group E', value: 278 },
        { name: 'Group F', value: 189 },
    ];
    const onClickDelete = (planId) => {
        setChosenPlansForViewing(chosenPlansForViewing.filter((plan) => plan.id !== planId));
    };
    const handleAddChosenPlan = () => {
        if (chosenPlansForViewing.filter((plan) => plan.id === selectedPlan.id).length > 0) {
            setSelectedPlanError('Plan already selected.');
        } else {
            setChosenPlansForViewing([
                ...chosenPlansForViewing,
                selectedPlan
            ]);
            setSelectedPlanError('');
        }
    };
    return (
        <div className="grid grid-flow-row sm:grid-flow-col sm:grid-cols-2 gap-4">
            <div className="overflow-x-scroll">
                <div className="w-full h-64 p-3 min-w-72">
                    <ResponsiveContainer width="100%" height="100%">
                        <PieChart>
                            <Pie
                                dataKey="value"
                                isAnimationActive={false}
                                data={data01}
                                fill="#8884d8"
                                label
                            >
                                {data01.map((entry, index) => (
                                    <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                                ))}
                            </Pie>
                            <Tooltip />
                        </PieChart>
                    </ResponsiveContainer>
                </div>
            </div>
            <div>
                {plansList.length > 0 && selectedPlan ? (
                    <div>
                        <h2 className="font-semibold mb-5 text-xl text-center sm:text-2xl">Plan Expense Stats</h2>
                        <div className="flex flex-col sm:flex-row">
                            <AutocompleteSelect
                                itemsList={plansList}
                                itemLabelKey="name"
                                selectedValue={selectedPlan}
                                setSelectedValue={setSelectedPlan}
                                placeholder="Search Plans"
                                customClasses="sm:ml-0 w-full sm:w-1/2 flex-grow"
                            />
                            <Button type="button" className="hover:bg-blue-500 bg-blue-400 inline justify-center w-full sm:w-auto sm:ml-2 mt-2 sm:mt-0" onClick={handleAddChosenPlan} processing={chosenPlansForViewing.length >= 10}>
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </Button>
                        </div>

                        {selectedPlanError ? (<p className="text-red-500 text-xs mt-1">{selectedPlanError}</p>) : ''}
                        {chosenPlansForViewing.length > 0 ? (
                            <Table customClass="mt-3" headers={['Plan Name', 'Remove']}>
                                {chosenPlansForViewing.map((plan, index) => (
                                    <tr key={index}>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <div className="flex items-center">
                                                <div className="ml-4">
                                                    <div className="text-sm font-medium text-gray-900">{plan.name}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <div className="flex items-center">
                                                <div className="ml-4">
                                                    <div className="text-sm font-medium text-gray-900">
                                                        <span onClick={() => {onClickDelete(plan.id)}}>
                                                            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 cursor-pointer" viewBox="0 0 20 20" fill="rgba(239, 68, 68, var(--tw-text-opacity))">
                                                                <path fillRule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clipRule="evenodd" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </Table>
                        ) : (<Alert className="mt-3" severity="info">Please select a plan to view expense data</Alert>)}
                    </div>
                ) : (<Alert className="mt-3" severity="info">You don't have any plans to get expense stats.</Alert>)}
            </div>
        </div>
    );
}

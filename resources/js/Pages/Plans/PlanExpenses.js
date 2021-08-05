import React, {useEffect, useState} from "react";
import Button from "@/Components/Button";
import CustomSwitch from "@/Components/Switch";
import {usePage} from "@inertiajs/inertia-react";
import AutocompleteSelect from "@/Components/AutocompleteSelect";
import {Alert} from "@material-ui/lab";
import NumberInput from "@/Components/NumberInput";
import {Inertia} from "@inertiajs/inertia";

export default function PlanExpenses({ planId }) {
    const { categoryList, errors, planMemberList } = usePage().props;
    const [isSharedExpense, setIsSharedExpense] = useState(false);
    const [selectedExpenseCategory, setSelectedExpenseCategory] = useState();
    const [expenseAmount, setExpenseAmount] = useState(0);
    const [sharedExpenseMembersWhoPaid, setSharedExpenseMembersWhoPaid ] = useState([]);
    const [sharedExpenseMemberToBeAdded, setSharedExpenseMemberToBeAdded] = useState();
    const [sharedExpenseMemberAmount, setSharedExpenseMemberAmount] = useState(0);
    const [sharedExpenseMembersPaidEqually, setSharedExpenseMembersPaidEqually ] = useState(true);
    const [sharedMemberExpenseError, setSharedMemberExpenseError] = useState('');

    useEffect(() => {
        if (categoryList.length > 0) {
            setSelectedExpenseCategory(categoryList[0]);
        }
        setSharedExpenseMemberToBeAdded(planMemberList[0]);
    },[]);

    const handleRecordExpense = () => {
        Inertia.post('/expense/add', {
            isSharedExpense: isSharedExpense,
            category: selectedExpenseCategory.name,
            amount: expenseAmount,
            planId: planId,
            sharedExpenseMembersPaidEqually: sharedExpenseMembersPaidEqually,
            sharedExpenseMembersWhoPaid: sharedExpenseMembersWhoPaid
        });
    }

    const addSharedPaymentMember = () => {
        if (sharedExpenseMembersWhoPaid.filter((member) => member.email === sharedExpenseMemberToBeAdded.email).length > 0) {
            setSharedMemberExpenseError('Member has already been added.');
        } else if(sharedExpenseMemberAmount <= 0) {
            setSharedMemberExpenseError('The amount has to be greater than 0.');
        } else {
            let totalAmount = 0;
            sharedExpenseMembersWhoPaid.forEach((member) => {
                totalAmount += member.amount
            })
            totalAmount += sharedExpenseMemberAmount;
            setExpenseAmount(totalAmount);
            setSharedExpenseMembersWhoPaid([
                ...sharedExpenseMembersWhoPaid,
                {
                    email: sharedExpenseMemberToBeAdded.email,
                    amount: sharedExpenseMemberAmount,
                    fullName: sharedExpenseMemberToBeAdded.fullName,
                }
            ]);
            setSharedExpenseMemberToBeAdded(planMemberList[0]);
            setSharedExpenseMemberAmount(0);
            setSharedMemberExpenseError('');
        }
    }

    const removeSharedExpenseMember = (email) => {
        setSharedMemberExpenseError('');
        setSharedExpenseMembersWhoPaid(sharedExpenseMembersWhoPaid.filter((member) => member.email !== email));
    }

    const getSingleCategoryExpenseForm = () => {
        return (
            <div className="mt-8 max-w-xl sm:max-w-2xl">
                <label className="block text-sm font-medium text-gray-700">
                    Choose Category:
                </label>
                {(categoryList.length > 0 && selectedExpenseCategory) ? (
                    <AutocompleteSelect
                        itemsList={categoryList}
                        itemLabelKey="name"
                        selectedValue={selectedExpenseCategory}
                        setSelectedValue={setSelectedExpenseCategory}
                        placeholder="Search Category"
                    />
                ) : (<Alert severity="info">Please create a category first!</Alert>)}
                <div className="mt-8">
                    <label htmlFor="expense_amt" className="block text-sm font-medium text-gray-700">
                        Expense Amount
                    </label>
                    <div className="mt-2">
                        <NumberInput
                            disabled={isSharedExpense && !sharedExpenseMembersPaidEqually}
                            elementId="expense_amt"
                            value={expenseAmount}
                            setValue={setExpenseAmount}
                            placeholder="Enter amount"
                        />
                        {errors.amount ? (<p className="text-red-500 text-xs mt-1">{errors.amount}</p>) : ''}
                    </div>
                </div>
                {isSharedExpense ? (
                    <>
                        <div className="mt-6">
                            <label className="inline-flex items-center mt-3 mb-3">
                                <span className="mr-2 text-gray-700">Shared members paid equally?</span>
                                <input type="checkbox" className="form-checkbox h-5 w-5 text-blue-600 rounded" checked={sharedExpenseMembersPaidEqually} onChange={() => {setSharedExpenseMembersPaidEqually(!sharedExpenseMembersPaidEqually)}}/>
                            </label>
                        </div>
                        {!sharedExpenseMembersPaidEqually ? (
                            <div className="mt-6">
                                <label htmlFor="expense_amt" className="block text-sm font-medium text-gray-700">
                                    Add member(s) who paid
                                </label>
                                <div className="flex flex-col sm:flex-row">
                                    <AutocompleteSelect
                                        itemsList={planMemberList}
                                        itemLabelKey="fullName"
                                        selectedValue={sharedExpenseMemberToBeAdded}
                                        setSelectedValue={setSharedExpenseMemberToBeAdded}
                                        placeholder="Search Member"
                                        customClasses="m-2 ml-2 sm:ml-0 w-full sm:w-1/2"
                                    />
                                    <NumberInput
                                        value={sharedExpenseMemberAmount}
                                        setValue={setSharedExpenseMemberAmount}
                                        placeholder="Enter amount"
                                        customClasses="m-2"
                                    />
                                    <Button className="hover:bg-blue-500 bg-blue-400 inline justify-center w-full sm:w-auto m-2" onClick={addSharedPaymentMember} processing={sharedExpenseMembersPaidEqually}>
                                        <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </Button>
                                </div>
                                {sharedMemberExpenseError ? (<p className="text-red-500 text-xs mb-1">{sharedMemberExpenseError}</p>) : ''}
                            </div>
                        ) : ''}
                        {sharedExpenseMembersWhoPaid.length > 0 && !sharedExpenseMembersPaidEqually ? (
                            <div className="flex flex-col">
                                <div className="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                    <div className="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                        <div className="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                            <table className="min-w-full divide-y divide-gray-200">
                                                <thead className="bg-gray-50">
                                                    <tr>
                                                        <th
                                                            scope="col"
                                                            className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                        >
                                                            Name
                                                        </th>
                                                        <th
                                                            scope="col"
                                                            className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                        >
                                                            Amount
                                                        </th>
                                                        <th
                                                            scope="col"
                                                            className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                        >
                                                            Remove
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody className="bg-white divide-y divide-gray-200">
                                                {sharedExpenseMembersWhoPaid.map((member, index) => (
                                                    <tr key={index}>
                                                        <td className="px-6 py-4 whitespace-nowrap">
                                                            <div className="flex items-center">
                                                                <div className="ml-4">
                                                                    <div className="text-sm font-medium text-gray-900">{member.fullName}</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap">
                                                            <div className="text-sm text-gray-900">{member.amount}</div>
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap text-blue-400">
                                                            <span onClick={() => {removeSharedExpenseMember(member.email)}}>
                                                                <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path fillRule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clipRule="evenodd" />
                                                                </svg>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                ))}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ) : ''}
                        {sharedExpenseMembersWhoPaid.length === 0 && !sharedExpenseMembersPaidEqually ? (
                            <Alert className="mt-2" severity="error">You have to add at least 1 member belonging to this plan!</Alert>
                        ) : ''}
                    </>
                ) : ''}
            </div>
        );
    }

    return (
        <div className="mt-12">
            <h2 className="font-semibold mb-5 text-xl sm:text-2xl">Record Expense</h2>
            <div className="mt-4">
                <CustomSwitch
                    isEnabled={isSharedExpense}
                    setIsEnabled={(value) => {
                        if (planMemberList.length > 2) {
                            setIsSharedExpense(value);
                        }
                    }}
                    labelText="Share equally among all members? "
                    shouldDisplayYesNo={true}
                />
                <p className="text-xs mb-1">Requires at least 2 members in your plan.</p>
            </div>
            <div className="mt-2">
                {getSingleCategoryExpenseForm()}
            </div>
            <div className="mt-4">
                <Button
                    className="hover:bg-blue-500 bg-blue-400 inline justify-center"
                    onClick={handleRecordExpense}
                    processing={
                        expenseAmount <= 0 ||
                        (isSharedExpense && planMemberList.length < 2) ||
                        (isSharedExpense && !sharedExpenseMembersPaidEqually && sharedExpenseMembersWhoPaid.length < 1)
                    }
                >
                    Record expense
                </Button>
            </div>
        </div>
    )
}

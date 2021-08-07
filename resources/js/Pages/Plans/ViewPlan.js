import React, {useEffect, useReducer} from "react";
import Authenticated from "@/Layouts/Authenticated";
import PlanContainer from "@/Pages/Plans/PlanContainer";
import SimpleTabs from "@/Components/SimpleTabs";
import PlanDetails from "@/Pages/Plans/PlanDetails";
import PlanUsers from "@/Pages/Plans/PlanUsers";
import PlanActivity from "@/Pages/Plans/PlanActivity";
import RecordPlanExpense from "@/Pages/Plans/RecordPlanExpense";
import PlanExpenses from "@/Pages/Plans/PlanExpenses";
import {initialRecordExpenseState, rootReducer} from "@/Pages/Plans/GlobalPlanStates/Reducer";

export const PlanContext = React.createContext();

export default function ViewPlan(props) {
    const {planDetails, categoryList, planMemberList} = props;
    const [state, dispatch] = useReducer(rootReducer, initialRecordExpenseState);

    const setSelectedExpenseCategory = (val) => {
        dispatch({
            ...state,
            selectedExpenseCategory: val
        });
    };

    useEffect(() => {
        if (categoryList.length > 0) {
            setSelectedExpenseCategory(categoryList[0]);
        }
    }, [categoryList]);

    useEffect(() => {
        dispatch({
            ...state,
            selectedExpenseCategory: categoryList.length > 0 ? categoryList[0] : null,
            sharedExpenseMemberToBeAdded: planMemberList[0]
        })
    },[]);

    return (
        <Authenticated
            auth={props.auth}
            errors={props.errors}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Plan: {planDetails.name}</h2>}
        >
            <PlanContext.Provider value={{state, dispatch}}>
                <PlanContainer>
                    <div className="m-4 p-6 rounded bg-gray-100">
                        <span className="font-bold">About: </span> <span className="font-light">{planDetails.description}</span>
                    </div>
                    <SimpleTabs
                        tabList={
                            [
                                {
                                    label: 'Plan Details',
                                    contents: (<PlanDetails id={planDetails.id}/>),
                                },
                                {
                                    label: 'Plan Users',
                                    contents: (<PlanUsers id={planDetails.id}/>),
                                },
                                {
                                    label: 'Plan Activity',
                                    contents: (<PlanActivity id={planDetails.id}/>),
                                },
                                {
                                    label: 'Record Expense',
                                    contents: (<RecordPlanExpense planId={planDetails.id}/>),
                                },
                                {
                                    label: 'Plan Expenses',
                                    contents: (<PlanExpenses planId={planDetails.id}/>),
                                }
                            ]
                        }
                    />
                </PlanContainer>
            </PlanContext.Provider>
        </Authenticated>
    );
}

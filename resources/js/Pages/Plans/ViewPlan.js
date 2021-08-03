import React from "react";
import Authenticated from "@/Layouts/Authenticated";
import PlanContainer from "@/Pages/Plans/PlanContainer";
import SimpleTabs from "@/Components/SimpleTabs";
import PlanDetails from "@/Pages/Plans/PlanDetails";
import PlanUsers from "@/Pages/Plans/PlanUsers";
import PlanActivity from "@/Pages/Plans/PlanActivity";
import PlanExpenses from "@/Pages/Plans/PlanExpenses";

export default function ViewPlan(props) {
    const {planDetails} = props;

    return (
        <Authenticated
            auth={props.auth}
            errors={props.errors}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Plan: {planDetails.name}</h2>}
        >
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
                                label: 'Plan Expense',
                                contents: (<PlanExpenses planId={planDetails.id}/>),
                            }
                        ]
                    }
                />
            </PlanContainer>
        </Authenticated>
    );
}

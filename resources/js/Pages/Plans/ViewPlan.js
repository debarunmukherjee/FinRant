import React from "react";
import Authenticated from "@/Layouts/Authenticated";
import PlanContainer from "@/Pages/Plans/PlanContainer";
import SimpleTabs from "@/Components/SimpleTabs";
import PlanDetails from "@/Pages/Plans/PlanDetails";
import PlanUsers from "@/Pages/Plans/PlanUsers";
import PlanActivity from "@/Pages/Plans/PlanActivity";

export default function ViewPlan(props) {
    const {planDetails} = props;

    return (
        <Authenticated
            auth={props.auth}
            errors={props.errors}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Plan: {planDetails.name}</h2>}
        >
            <PlanContainer>
                {planDetails.description}
                <SimpleTabs
                    tabList={
                        [
                            {
                                label: 'Plan Details',
                                contents: (<PlanDetails id={planDetails.id}/>),
                            },
                            {
                                label: 'Plan Users',
                                contents: (<PlanUsers/>),
                            },
                            {
                                label: 'Plan Activity',
                                contents: (<PlanActivity/>),
                            }
                        ]
                    }
                />
            </PlanContainer>
        </Authenticated>
    );
}

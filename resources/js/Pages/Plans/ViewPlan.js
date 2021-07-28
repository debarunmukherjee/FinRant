import React from "react";
import Authenticated from "@/Layouts/Authenticated";
import PlanContainer from "@/Pages/Plans/PlanContainer";

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
            </PlanContainer>
        </Authenticated>
    );
}

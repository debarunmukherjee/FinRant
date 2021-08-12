import React from "react";
import {Divider, Tooltip} from "@material-ui/core";
import {InertiaLink, usePage} from "@inertiajs/inertia-react";
import {Alert} from "@material-ui/lab";

export default function GeneralStats() {
    const { createdPlans, memberPlans, totalSavings, totalPending, hasUserSetBudgetForCurrentMonth } = usePage().props;
    const getToolTipTitleForPendingAmount = () => {
        if (Number(totalPending) > 0) {
            return 'You are in a debt and need to payback a total of this amount to other users, across various plans.';
        } else if (Number(totalPending) < 0) {
            return 'Other people owe you money and you will receive this amount from other users, across various plans.';
        } else {
            return "You don't have any pending dues 🎉.";
        }
    }
    return (
        <div className="mb-8">
            <h3 className="text-3xl mt-7">Your Finance Summary</h3>
            <Divider className="w-2/3" style={{marginTop: '0.75rem', marginBottom: '1rem'}} />
            {!hasUserSetBudgetForCurrentMonth ? (
                <Alert className="mt-3" severity="info">You have not set this month's budget. You have until the end of the month to do so, failing which it will be recorded as 0. You can set your budget by heading over to the <InertiaLink href={route('user.profile')}><b className="text-blue-600">Profile Section</b></InertiaLink>.</Alert>
            ) : ''}
            <div className="mt-8 text-center p-11 rounded bg-gradient-to-r from-yellow-400 via-red-500 to-pink-500 shadow-xl">
                <div className="grid grid-flow-row sm:grid-flow-col sm:grid-cols-3 gap-4">
                    <div>
                        <p className="text-lg screen-998:text-2xl sm:items-center flex flex-col sm:flex-row">
                            Total Plans:
                            <Tooltip title="Total number of plans you are part of, including member and creator.">
                                <span className="cursor-pointer bg-red-100 text-purple-600 shadow-inner rounded ml-3 p-2">
                                    {createdPlans.length + memberPlans.length}
                                </span>
                            </Tooltip>
                        </p>
                    </div>
                    <div>
                        <p className="text-lg screen-998:text-2xl sm:items-center flex flex-col sm:flex-row">
                            Total Savings:
                            <Tooltip title="Total savings you have made, calculated by total monthly budget - total expenditure. Negative value indicates your expenditure exceeded your total budget 😞">
                                <span className="cursor-pointer bg-red-100 text-purple-600 shadow-inner rounded ml-3 p-2">
                                    {totalSavings}
                                </span>
                            </Tooltip>
                        </p>
                    </div>
                    <div>
                        <p className="text-lg screen-998:text-2xl sm:items-center flex  flex-col sm:flex-row">
                            Total Pending:
                            <Tooltip title={getToolTipTitleForPendingAmount()}>
                                <span className="justify-center items-center inline-flex cursor-pointer bg-red-100 text-purple-600 shadow-inner rounded ml-3 p-2">
                                    <span>{Math.abs(Number(totalPending))}</span>
                                    <span className="ml-1">
                                        {Number(totalPending) > 0 ? (
                                            <span className="text-red-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z" />
                                                </svg>
                                            </span>
                                        ) : Number(totalPending) < 0 ? (
                                            <span className="text-green-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z" />
                                                </svg>
                                            </span>
                                        ) : ''}
                                    </span>
                                </span>
                            </Tooltip>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    )
}

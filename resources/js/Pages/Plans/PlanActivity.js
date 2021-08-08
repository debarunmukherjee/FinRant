import React, {useEffect, useState} from "react";
import {Divider} from "@material-ui/core";
import API from "@/Utils/API";

export default function PlanActivity({ planId }) {
    const [ planActivities, setPlanActivities ] = useState([]);
    const [ isLoading, setIsLoading ] = useState(true);

    const fetchAllPlanActivities = async () => {
        setIsLoading(true);
        const res = await API.get(`/plan-activities?planId=${planId}`);
        setIsLoading(false);
        setPlanActivities(res.data.activities);
    }

    useEffect(() => {
        fetchAllPlanActivities();
    }, []);

    return (
        <div>
            <h2 className="font-semibold mt-5 text-xl sm:text-2xl">
                <span className="align-top">Plan Activities</span>
                <button
                    onClick={fetchAllPlanActivities}
                    type="button"
                    className={`ml-2 inline-flex items-center justify-center rounded-full h-9 w-9 transition duration-500 ease-in-out text-white ${isLoading ? 'bg-gray-200' : 'bg-blue-500'} ${isLoading ? '' : 'hover:bg-blue-400'} focus:outline-none`}>
                    <svg xmlns="http://www.w3.org/2000/svg" className={`h-6 w-6 ${isLoading ? 'animate-spin' : ''}`} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </h2>
            <p className="text-xs mt-1">
                The FinRant bot 🤖 will post all the activities taking place in the plan in here.
            </p>
            <Divider className="w-2/3" style={{marginTop: '0.75rem', marginBottom: '0.75rem'}} />
            <div className={`border-blue-300 bg-blue-50 h-60-vh rounded shadow-md overflow-y-auto p-6 ${isLoading ? 'flex justify-center items-center' : ''}`}>
                {isLoading ? (
                    <div className="border border-blue-300 shadow rounded-md p-4 max-w-sm w-full mx-auto">
                        <div className="animate-pulse flex space-x-4">
                            <div className="rounded-full bg-blue-400 h-12 w-12"/>
                            <div className="flex-1 space-y-4 py-1">
                                <div className="h-4 bg-blue-400 rounded w-3/4"/>
                                <div className="space-y-2">
                                    <div className="h-4 bg-blue-400 rounded"/>
                                    <div className="h-4 bg-blue-400 rounded w-5/6"/>
                                </div>
                            </div>
                        </div>
                    </div>
                ) : (
                    Object.keys(planActivities).map((key) => (
                            <>
                                <div className="mt-8" key={key}>
                                    <div className="flex items-end justify-end">
                                        <div className="flex flex-col space-y-2 text-xs xs:text-sm max-w-3/4 mx-2 order-1 items-end">
                                            {planActivities[key].map((activity, index) => (
                                                <div>
                                                    <p
                                                        dangerouslySetInnerHTML={{__html: activity.message}}
                                                        className={`px-4 py-2 rounded-lg inline-block ${index === (planActivities[key].length-1) ? 'rounded-br-none' : ''} bg-blue-600 text-white `}
                                                    >
                                                    </p>
                                                </div>
                                            ))}
                                        </div>
                                        <img
                                            src={`/storage/images/bot.png`}
                                            alt="Bot Image"
                                            className="w-8 h-8 order-2"
                                        />
                                    </div>
                                </div>
                            </>
                        ))
                    )
                }
            </div>
        </div>
    )
}

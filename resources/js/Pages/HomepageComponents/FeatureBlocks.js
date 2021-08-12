import React from 'react';

function FeaturesBlocks() {
    return (
        <section className="relative">
            <div className="absolute inset-0 top-1/2 md:mt-24 lg:mt-0 bg-gray-900 pointer-events-none" aria-hidden="true"/>
            <div className="absolute left-0 right-0 bottom-0 m-auto w-px p-px h-20 bg-gray-200 transform translate-y-1/2"/>

            <div className="relative max-w-6xl mx-auto px-4 sm:px-6">
                <div className="py-12 md:py-20">
                    <div className="max-w-3xl mx-auto text-center pb-12 md:pb-20">
                        <h2 className="text-3xl sm:text-4xl font-bold mb-4">How FinRant works</h2>
                        <p className="text-xl text-gray-600">
                            The FinRant app lets you add expenses under different plans as per your needs. You can collaborate with other members to keep track of shared expenses, split expenses and set individual budgets under various categories.
                        </p>
                    </div>
                    <div className="max-w-sm mx-auto grid gap-6 md:grid-cols-2 lg:grid-cols-3 items-start md:max-w-2xl lg:max-w-none">
                        <div className="relative flex flex-col items-center p-6 bg-white rounded shadow-xl self-stretch">
                            <p className="p-3 rounded-full bg-blue-600 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </p>
                            <h4 className="text-xl font-bold leading-snug tracking-tight mb-1">Create Plans</h4>
                            <p className="text-gray-600 text-center">Create plans according to your expense needs to manage your finance in a granular manner.</p>
                        </div>
                        <div className="relative flex flex-col items-center p-6 bg-white rounded shadow-xl self-stretch">
                            <p className="p-3 rounded-full bg-blue-600 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </p>
                            <h4 className="text-xl font-bold leading-snug tracking-tight mb-1">Join and Collaborate</h4>
                            <p className="text-gray-600 text-center">Invite other members to join your expense plan to manage finance of your entire group at a single place.</p>
                        </div>
                        <div className="relative flex flex-col items-center p-6 bg-white rounded shadow-xl self-stretch">
                            <p className="p-3 rounded-full bg-blue-600 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </p>
                            <h4 className="text-xl font-bold leading-snug tracking-tight mb-1">Track your dues</h4>
                            <p className="text-gray-600 text-center">FinRant calculates the amount of money you owe or will receive from other members. It also shows whom and how much you need to pay to settle all your dues.</p>
                        </div>
                        <div className="relative flex flex-col items-center p-6 bg-white rounded shadow-xl self-stretch">
                            <p className="p-3 rounded-full bg-blue-600 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </p>
                            <h4 className="text-xl font-bold leading-snug tracking-tight mb-1">Set Budgets</h4>
                            <p className="text-gray-600 text-center">Set monthly budgets and compare it with your current expenses. You can also set individual budgets for categories in a single plan.</p>
                        </div>
                        <div className="relative flex flex-col items-center p-6 bg-white rounded shadow-xl self-stretch">
                            <p className="p-3 rounded-full bg-blue-600 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </p>
                            <h4 className="text-xl font-bold leading-snug tracking-tight mb-1">Secure Payment</h4>
                            <p className="text-gray-600 text-center">Settle your dues or transfer money to other members directly from our app with our secured payment gateway.</p>
                        </div>
                        <div className="relative flex flex-col items-center p-6 bg-white rounded shadow-xl self-stretch">
                            <p className="p-3 rounded-full bg-blue-600 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 13v-1m4 1v-3m4 3V8M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                                </svg>
                            </p>
                            <h4 className="text-xl font-bold leading-snug tracking-tight mb-1">Visual Reports</h4>
                            <p className="text-gray-600 text-center">Get visual reports of your various financial activities on your personalised dashboard and always keep your financial information in the loop.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}

export default FeaturesBlocks;

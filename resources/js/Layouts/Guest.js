import ApplicationLogo from '../Components/ApplicationLogo';
import React from 'react';
import { InertiaLink } from '@inertiajs/inertia-react';

export default function Guest({ children }) {
    return (
        <div className="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <InertiaLink href="/">
                    <ApplicationLogo imgWidth={'20rem'} />
                </InertiaLink>
            </div>

            <div className="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {children}
            </div>
        </div>
    );
}

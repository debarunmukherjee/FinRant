import React, { useState, useEffect } from 'react';
import ApplicationLogo from "@/Components/ApplicationLogo";
import {InertiaLink} from "@inertiajs/inertia-react";

export default function LandingHeader() {

    const [top, setTop] = useState(true);

    useEffect(() => {
        const scrollHandler = () => {
            window.pageYOffset > 10 ? setTop(false) : setTop(true)
        };
        window.addEventListener('scroll', scrollHandler);
        return () => window.removeEventListener('scroll', scrollHandler);
    }, [top]);

    return (
        <header className={`fixed w-full z-30 md:bg-opacity-90 transition duration-300 ease-in-out ${!top && 'bg-white blur shadow-lg'}`}>
            <div className="max-w-6xl mx-auto px-5 sm:px-6">
                <div className="flex items-center justify-between h-16 md:h-20">
                    <div className="flex-shrink-0 mr-4">
                        <InertiaLink href="/">
                            <ApplicationLogo imgWidth={'8rem'}/>
                        </InertiaLink>
                    </div>
                    <nav className="flex flex-grow">
                        <ul className="flex flex-grow justify-end flex-wrap items-center">
                            <li>
                                <InertiaLink
                                    href={route('login')}
                                    className="transition duration-150 ease-in-out border-transparent hover:border-gray-300 border-b-2 font-medium text-gray-600 hover:text-gray-900 px-2 py-0.5 sm:px-5 sm:py-3 flex items-center transition duration-150 ease-in-out"
                                >
                                        Sign in
                                </InertiaLink>
                            </li>
                            <li>
                                <InertiaLink href={route('register')} className="hidden small-mobile:flex rounded-full font-medium text-white bg-blue-500 hover:bg-blue-600 sm:mt-0 ml-3 px-2 py-0.5 sm:px-5 sm:py-3 items-center transition duration-150 ease-in-out">
                                    <span>Sign up</span>
                                    <svg className="w-3 h-3 fill-current flex-shrink-0 ml-2 -mr-1" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M11.707 5.293L7 .586 5.586 2l3 3H0v2h8.586l-3 3L7 11.414l4.707-4.707a1 1 0 000-1.414z" fillRule="nonzero" />
                                    </svg>
                                </InertiaLink>
                            </li>
                        </ul>

                    </nav>

                </div>
            </div>
        </header>
    );
}

import ApplicationLogo from '../Components/ApplicationLogo';
import Dropdown from '../Components/Dropdown';
import NavLink from '../Components/NavLink';
import React, {useEffect, useState} from 'react';
import ResponsiveNavLink from '../Components/ResponsiveNavLink';
import {InertiaLink, usePage} from '@inertiajs/inertia-react';
import Notification from "@/Components/Notification";
import { useSnackbar } from 'notistack';

export default function Authenticated({ auth, header, children }) {
    const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);
    const [openSuccess, setOpenSuccess] = useState(false);
    const [openMessage, setOpenMessage] = useState(false);
    const [openError, setOpenError] = useState(false);
    const [ totalInvites, setTotalInvites ] = useState(0);
    const { flash } = usePage().props;

    const { enqueueSnackbar } = useSnackbar();

    useEffect(() => {
        if (flash.message) {
            setOpenMessage(true);
        }
        if (flash.success) {
            setOpenSuccess(true);
        }
        if (flash.error) {
            setOpenError(true);
        }
    }, [flash]);

    useEffect(() => {
        setTotalInvites(auth.user.pendingInvites);
        Echo.private('plan-invite.' + auth.user.id)
            .listen('NewInvite', (inviteDetails) => {
                setTotalInvites(inviteDetails.totalInvites);
                enqueueSnackbar(`${inviteDetails.inviterName} invited you to join the plan ${inviteDetails.planName}`, {
                    variant: 'success',
                });
            });
        Echo.private('reject-invite.' + auth.user.id)
            .listen('RejectInvite', (inviteDetails) => {
                enqueueSnackbar(`${inviteDetails.invitedName} rejected your invite to join the plan ${inviteDetails.planName}`, {
                    variant: 'error',
                });
            });
        Echo.private('accept-invite.' + auth.user.id)
            .listen('AcceptInvite', (inviteDetails) => {
                enqueueSnackbar(`${inviteDetails.invitedName} accepted your invite to join the plan ${inviteDetails.planName}`, {
                    variant: 'success',
                });
            });
    },[]);

    return (
        <div className="min-h-screen bg-gray-100">
            <nav className="bg-white border-b border-gray-100">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-16">
                        <div className="flex">
                            <div className="flex-shrink-0 flex items-center">
                                <InertiaLink href="/">
                                    <ApplicationLogo imgWidth={'8rem'} />
                                </InertiaLink>
                            </div>

                            <div className="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink href={route('dashboard')} active={route().current('dashboard')}>
                                    Dashboard
                                </NavLink>
                            </div>
                            <div className="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink href={route('plans')} active={route().current('plans')}>
                                    Plans
                                </NavLink>
                            </div>
                        </div>

                        <div className="hidden sm:flex sm:items-center sm:ml-6">
                            <div className="ml-3 relative">
                                <Dropdown>
                                    <Dropdown.Trigger>
                                        <span className="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                className="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                {auth.user.first_name}

                                                <svg
                                                    className="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fillRule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clipRule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </Dropdown.Trigger>

                                    <Dropdown.Content>
                                        <Dropdown.Link href={route('logout')} method="post" as="button">
                                            Log Out
                                        </Dropdown.Link>
                                        <Dropdown.Link href={route('user.profile')} method="get" as="button">
                                            Profile
                                        </Dropdown.Link>
                                        <Dropdown.Link href={route('invites.view')} method="get" as="button">
                                            <div className="flex mt-2">
                                                Plan Invites
                                                <p className="ml-auto">
                                                    <span className="relative inline-block">
                                                        <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.1} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                        </svg>
                                                        <span className="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-3 bg-blue-400 rounded-full">
                                                            {totalInvites}
                                                        </span>
                                                    </span>
                                                </p>
                                            </div>
                                        </Dropdown.Link>
                                    </Dropdown.Content>
                                </Dropdown>
                            </div>
                        </div>

                        <div className="-mr-2 flex items-center sm:hidden">
                            <button
                                onClick={() => setShowingNavigationDropdown((previousState) => !previousState)}
                                className="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                            >
                                <svg className="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        className={!showingNavigationDropdown ? 'inline-flex' : 'hidden'}
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        className={showingNavigationDropdown ? 'inline-flex' : 'hidden'}
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div className={(showingNavigationDropdown ? 'block' : 'hidden') + ' sm:hidden'}>
                    <div className="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink
                            href={route('dashboard')}
                            active={route().current('dashboard')}
                        >
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            href={route('plans')}
                            active={route().current('plans')}
                        >
                            Plans
                        </ResponsiveNavLink>
                    </div>

                    <div className="pt-4 pb-1 border-t border-gray-200">
                        <div className="px-4">
                            <div className="font-medium text-base text-gray-800">{auth.user.first_name}</div>
                            <div className="font-medium text-sm text-gray-500">{auth.user.email}</div>
                        </div>

                        <div className="mt-3 space-y-1">
                            <ResponsiveNavLink method="post" href={route('logout')} as="button">
                                Log Out
                            </ResponsiveNavLink>
                            <ResponsiveNavLink method="get" href={route('user.profile')} as="button">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink method="get" href={route('invites.view')} as="button">
                                Plan Invites
                                <p className="ml-auto">
                                    <span className="relative inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.1} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <span className="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-3 bg-blue-400 rounded-full">
                                            {totalInvites}
                                        </span>
                                    </span>
                                </p>
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            {header && (
                <header className="bg-white shadow">
                    <div className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">{header}</div>
                </header>
            )}

            <main>{children}</main>
            {flash.message ? (<Notification message={flash.message} open={openMessage} setOpen={setOpenMessage} severity="info"/>) : ''}
            {flash.success ? (<Notification message={flash.success} open={openSuccess} setOpen={setOpenSuccess} severity="success"/>) : ''}
            {flash.error ? (<Notification message={flash.error} open={openError} setOpen={setOpenError} severity="error"/>) : ''}
        </div>
    );
}

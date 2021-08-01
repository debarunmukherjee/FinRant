import React, {useEffect, useState} from "react";
import {usePage} from "@inertiajs/inertia-react";
import {Inertia} from "@inertiajs/inertia";
import Button from "@/Components/Button";
import {Grid} from "@material-ui/core";
import TelegramIcon from '@material-ui/icons/Telegram';

export default function PlanUsers({ id }) {
    const { errors } = usePage().props;
    const [inviteUserEmail, setInviteUserEmail] = useState('');

    useEffect(() => {
        if (!errors.inviteEmail) {
            setInviteUserEmail('');
        }
    },[errors]);

    const handleInviteUser = () => {
        Inertia.post('/invite/user', {
            inviteEmail: inviteUserEmail,
            planId: id
        })
    }

    return (
        <div>
            <Grid container>
                <Grid item sm={6} xs={12}>
                    <div className="flex flex-col mt-6">
                        <label htmlFor="invite-email" className="block text-base font-semibold text-gray-700 mb-3">
                            Invite a friend to join your plan now!
                        </label>
                        <div className="flex sm:flex-row flex-col mr-0 sm:space-x-4 space-x-0 space-y-4 sm:space-y-0">
                            <input
                                id="invite-email"
                                type="email"
                                className="w-auto focus:ring-indigo-500 focus:border-indigo-500 flex-1 w-auto inline rounded sm:text-sm border-gray-300"
                                placeholder="abc@example.com"
                                value={inviteUserEmail}
                                onChange={(e) => {setInviteUserEmail(e.target.value)}}
                            />
                            <Button className="hover:bg-blue-500 bg-blue-400 inline" onClick={handleInviteUser}>
                                Invite
                                <TelegramIcon/>
                            </Button>
                        </div>
                    </div>
                    {errors.inviteEmail ? (<p className="text-red-500 text-xs mt-1">{errors.inviteEmail}</p>) : ''}
                </Grid>
            </Grid>
        </div>
    )
}

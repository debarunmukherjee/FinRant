import React, {useEffect, useState} from "react";
import {usePage} from "@inertiajs/inertia-react";
import {Inertia} from "@inertiajs/inertia";
import Button from "@/Components/Button";
import {Divider, Grid} from "@material-ui/core";
import TelegramIcon from '@material-ui/icons/Telegram';
import UserMemberCard from "@/Pages/Plans/UserMemberCard";
import {Alert} from "@material-ui/lab";

export default function PlanUsers({ id }) {
    const { errors, planRole, planMemberList } = usePage().props;
    const planCreatorDetails = planMemberList.filter((member) => member.role === 'creator')[0];
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
            {planRole === 'creator' ? (
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
            ) : ''}
            <h3 className="text-2xl mt-7">Plan created by</h3>
            <Divider className="w-2/3" style={{marginTop: '0.75rem', marginBottom: '0.5rem'}} />
            <div className="flex">
                <UserMemberCard
                    fullName={planCreatorDetails.fullName}
                    profileImgUrl={planCreatorDetails.avatar}
                    role="creator"
                />
            </div>
            <h3 className="text-2xl mt-7">Plan Members</h3>
            <Divider className="w-2/3" style={{marginTop: '0.75rem', marginBottom: '0.5rem'}} />
            <div className="flex flex-wrap">
                {planMemberList.length > 1 ? planMemberList.map((member, index) => {
                    if (member.role !== 'creator') {
                        return (
                            <UserMemberCard
                                key={index}
                                fullName={member.fullName}
                                profileImgUrl={member.avatar}
                                role="member"
                            />
                        );
                    }
                }) : (<Alert severity="info">You have added no members to this plan.</Alert>)}
            </div>
        </div>
    )
}

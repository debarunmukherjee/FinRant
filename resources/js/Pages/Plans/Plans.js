import Authenticated from '@/Layouts/Authenticated';
import React, {useState} from 'react';
import Button from "@/Components/Button";
import Modal from "@/Components/Modal";
import {Inertia} from "@inertiajs/inertia";
import Notification from "@/Components/Notification";
import CardWithNavButton from "@/Components/Card";
import {Box} from "@material-ui/core";
import PlanContainer from "@/Pages/Plans/PlanContainer";

export default function Plans(props) {
    const [openCreatePlanForm, setOpenCreatePlanForm] = useState(false);
    const [newPlanName, setNewPlanName] = useState('');
    const [newPlanDescription, setNewPlanDescription] = useState('');
    const [showNotification, setShowNotification] = useState(false);
    const [notificationDetails, setNotificationDetails] = useState({});

    const {plans} = props;

    const handleNewPlanNameChange = (e) => {
        setNewPlanName(e.target.value);
    }

    const handleNewPlanDescriptionChange = (e) => {
        setNewPlanDescription(e.target.value);
    }

    const openCreatePlanFormModal = () => {
        setOpenCreatePlanForm(true);
    }
    const handleCreatePlanClick = () => {
        Inertia.post(
            '/create-plan',
            {
                name: newPlanName,
                description: newPlanDescription,
            }
        );
        setOpenCreatePlanForm(false);
        setShowNotification(true);
        setNotificationDetails({
            severity: 'success',
            message: 'Plan successfully created!',
        })
    }
    return (
        <Authenticated
            auth={props.auth}
            errors={props.errors}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Plans</h2>}
        >
            <PlanContainer>
                <Button className={'bg-blue-400'} onClick={openCreatePlanFormModal}>
                    Create Plan
                    <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </Button>
                <h3 className="text-2xl mt-7">Plans created by you</h3>
                {plans.length > 0 ? (
                    <Box display="flex" flexWrap="wrap">
                        {plans.map((plan) => (
                            <CardWithNavButton
                                key={plan.id}
                                title={plan.name}
                                body={plan.description}
                                subTitle={plan.created_at.split('T')[0]}
                                buttonText="View Plan"
                                navLink={`/plan/${plan.id}`}
                            />
                        ))}
                    </Box>
                ) : 'You have not created any plans.'}
            </PlanContainer>
            <Modal open={openCreatePlanForm} setOpen={setOpenCreatePlanForm} title={'Create Plan'} actionText={'Create'} onClickAction={handleCreatePlanClick}>
                <div>
                    <label htmlFor="plan_name" className="block text-sm font-medium text-gray-700">
                        Plan Name
                    </label>
                    <div className="mt-1">
                        <input
                            type="text"
                            name="name"
                            id="plan_name"
                            className="w-auto focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded sm:text-sm border-gray-300"
                            placeholder="Family Expenses"
                            value={newPlanName}
                            onChange={handleNewPlanNameChange}
                        />
                    </div>
                    <p className="mt-2 text-sm text-gray-500">
                        A short and sweet name for your plan.
                    </p>
                </div>
                <div className="mt-2">
                    <label htmlFor="plan_description" className="block text-sm font-medium text-gray-700">
                        Plan Description
                    </label>
                    <div className="mt-1">
                      <textarea
                          id="plan_description"
                          name="description"
                          rows={3}
                          className="shadow-sm w-auto focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"
                          placeholder="A brief description about your plan"
                          value={newPlanDescription}
                          onChange={handleNewPlanDescriptionChange}
                      />
                    </div>
                    <p className="mt-2 text-sm text-gray-500">
                        A brief description about your plan.
                    </p>
                </div>
            </Modal>
            {showNotification ? (
                <Notification message={notificationDetails.message} severity={notificationDetails.severity}/>
            ) : ''}
        </Authenticated>
    );
}

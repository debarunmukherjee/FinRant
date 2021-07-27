import Authenticated from '@/Layouts/Authenticated';
import React, {useState} from 'react';
import Button from "@/Components/Button";
import Modal from "@/Components/Modal";

export default function Plans(props) {
    const [openCreatePlanForm, setOpenCreatePlanForm] = useState(false);
    const openCreatePlanFormModal = () => {
        setOpenCreatePlanForm(true);
    }
    const handleCreatePlanClick = () => {
        setOpenCreatePlanForm(false);
    }
    return (
        <Authenticated
            auth={props.auth}
            errors={props.errors}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Plans</h2>}
        >
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <Button className={'bg-blue-400'} onClick={openCreatePlanFormModal}>Create Plan</Button>
                        </div>
                    </div>
                </div>
            </div>
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
                          defaultValue={''}
                      />
                    </div>
                    <p className="mt-2 text-sm text-gray-500">
                        A brief description about your plan.
                    </p>
                </div>
            </Modal>
        </Authenticated>
    );
}

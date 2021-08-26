import React, {useEffect, useState} from "react";
import {Divider} from "@material-ui/core";
import Button from "@/Components/Button";
import Modal from "@/Components/Modal";
import AutocompleteSelect from "@/Components/AutocompleteSelect";
import {Alert} from "@material-ui/lab";
import NumberInput from "@/Components/NumberInput";
import {usePage} from "@inertiajs/inertia-react";
import {Inertia} from "@inertiajs/inertia";

export default function MemberExpenseTransaction({ planId }) {
    const { categoryList, errors, planMemberList } = usePage().props;
    const [ openPaymentModal, setOpenPaymentModal ] = useState(false);
    const [ selectedUserToPay, setSelectedUserToPay ] = useState();
    const [ selectedExpenseCategoryToPay, setSelectedExpenseCategoryToPay ] = useState();
    const [ amountToPay, setAmountToPay ] = useState(0);
    const [ userPassword, setUserPassword ] = useState('');
    const [ userPasswordError, setUserPasswordError ] = useState('');
    const [ selectedUserToPayError, setSelectedUserToPayError ] = useState('');
    const [ selectedExpenseCategoryToPayError, setSelectedExpenseCategoryToPayError ] = useState('');
    const [ amountToPayError, setAmountToPayError ] = useState('');
    const [ upiId, setUpiId ] = useState('');
    const [ upiIdError, setUpiIdError ] = useState('');

    useEffect(() => {
        setSelectedUserToPay(planMemberList[0]);
        if (categoryList.length > 0) {
            setSelectedExpenseCategoryToPay(categoryList[0]);
        }
    },[]);

    const resetAllErrorsAndStates = () => {
        setSelectedUserToPay(planMemberList[0]);
        setAmountToPay(0);
        setUserPassword('');
        setUpiId('');
        setUserPasswordError('');
        setSelectedUserToPayError('');
        setAmountToPayError('');
        setSelectedExpenseCategoryToPayError('');
        setUpiIdError('');
        setOpenPaymentModal(false);
        if (categoryList.length > 0) {
            setSelectedExpenseCategoryToPay(categoryList[0]);
        }
    }
    useEffect(() => {
        if (errors.password || errors.selectedUserEmail || errors.amount || errors.category || errors.upiId) {
            setUserPasswordError(errors.password);
            setSelectedUserToPayError(errors.selectedUserEmail);
            setAmountToPayError(errors.amount);
            setSelectedExpenseCategoryToPayError(errors.category);
            setUpiIdError(errors.upiId);
        } else {
            resetAllErrorsAndStates();
        }
    },[errors]);

    const handleMemberPaymentClick = (isOpen) => {
        if (!isOpen) {
            resetAllErrorsAndStates();
        }
        setOpenPaymentModal(isOpen);
    };

    const handlePayment = () => {
        Inertia.post('/user/plan/fund-transfer', {
            selectedUserEmail: selectedUserToPay.email,
            category: selectedExpenseCategoryToPay.name,
            amount: amountToPay,
            password: userPassword,
            planId: planId,
            upiId: upiId
        });
    }

    return (
        <div>
            <h2 className="font-semibold mt-5 text-xl sm:text-2xl">Transfer money</h2>
            <p className="text-xs mt-1">
                You can transfer money through our payment portal to another plan member.<br/>
                Please note that it will be recorded as an expense under the specified category.<br/>
                It cannot be used to settle any existing dues. Please visit the <b>PLAN EXPENSES</b> tab to settle any existing dues.
            </p>
            <Divider className="w-2/3" style={{marginTop: '0.75rem', marginBottom: '0.5rem'}} />
            <div className="mt-5">
                <Button
                    className="hover:bg-blue-500 bg-blue-400 inline justify-center"
                    onClick={() => setOpenPaymentModal(true)}
                    processing={planMemberList.length < 2}
                >
                    Pay a member
                </Button>
            </div>
            <p className="text-xs mt-2">Requires at least 2 members in your plan.</p>
            <Modal title="Pay a member" open={openPaymentModal} setOpen={handleMemberPaymentClick} actionText="Proceed To Payment" onClickAction={handlePayment}>
                <div className="mt-4">
                    <label className="block text-sm font-medium text-gray-700 mb-1">
                        Choose Plan Member
                    </label>
                    <AutocompleteSelect
                        itemsList={planMemberList}
                        itemLabelKey="fullName"
                        selectedValue={selectedUserToPay}
                        setSelectedValue={(val) => setSelectedUserToPay(val)}
                        placeholder="Search Member"
                    />
                    {selectedUserToPayError ? (<p className="text-red-500 text-xs mb-1">{selectedUserToPayError}</p>) : ''}
                    <label className="block text-sm font-medium text-gray-700 mb-1 mt-4">
                        Choose Expense Category
                    </label>
                    {categoryList.length > 0 && selectedExpenseCategoryToPay ? (
                        <AutocompleteSelect
                            itemsList={categoryList}
                            itemLabelKey="name"
                            selectedValue={selectedExpenseCategoryToPay}
                            setSelectedValue={setSelectedExpenseCategoryToPay}
                            placeholder="Search Category"
                        />
                    ) : (<Alert severity="info" className="max-w-full">Please create a category first!</Alert>)}
                    {selectedExpenseCategoryToPayError ? (<p className="text-red-500 text-xs mb-1">{selectedExpenseCategoryToPayError}</p>) : ''}
                    <label htmlFor="custom-member-payment-amount" className="block text-sm font-medium text-gray-700 mt-4">
                        Enter Amount
                    </label>
                    <NumberInput
                        elementId="custom-member-payment-amount"
                        value={amountToPay}
                        setValue={setAmountToPay}
                        placeholder="Enter amount"
                    />
                    {amountToPayError ? (<p className="text-red-500 text-xs mb-1">{amountToPayError}</p>) : ''}
                </div>
                <div className="mt-4">
                    <Divider style={{marginTop: '0.75rem', marginBottom: '0.75rem'}} />
                    <p className="block font-medium">
                        <span className="text-gray-700">Email: </span> <span className="font-semibold">{selectedUserToPay ? selectedUserToPay.email : ''}</span>
                    </p>
                    <p className="block font-medium mt-5">
                        <span className="text-gray-700">Full Name: </span>
                        <span className="font-semibold">
                            {selectedUserToPay ? selectedUserToPay.fullName : ''}
                        </span>
                    </p>
                    <p className="block font-medium mt-5">
                        <span className="text-gray-700">Amount: </span> <span className="font-semibold">{amountToPay}</span>
                    </p>
                </div>
                <div className="mt-2">
                    <Divider style={{marginTop: '0.75rem', marginBottom: '0.75rem'}} />
                    <label htmlFor="user_password" className="block font-medium text-gray-700">
                        Enter UPI ID <img src="/storage/images/upi.png" alt="upi logo" width={50} className="inline"/>
                    </label>
                    <div className="mt-1">
                        <input
                            type="text"
                            className="w-auto focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded sm:text-sm border-gray-300"
                            value={upiId}
                            onChange={(e) => {
                                setUpiId(e.target.value);
                            }}
                        />
                        {upiIdError ? (<p className="text-red-500 text-xs mt-1">{upiIdError}</p>) : ''}
                    </div>
                    <label htmlFor="user_password" className="block font-medium text-gray-700 mt-4">
                        Enter Password
                    </label>
                    <div className="mt-1">
                        <input
                            type="password"
                            className="w-auto focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded sm:text-sm border-gray-300"
                            value={userPassword}
                            onChange={(e) => {
                                setUserPassword(e.target.value);
                            }}
                        />
                    </div>
                    <p className="mt-2 text-sm text-gray-500">
                        Enter password to continue payment.
                    </p>
                </div>
                {userPasswordError ? <Alert className="mt-2" severity="error">{userPasswordError}</Alert> : ''}
            </Modal>
        </div>
    );
}

import React, {useEffect, useState} from "react";
import Table from "@/Components/Table";
import PaymentIcon from "@material-ui/icons/Payment";
import {Alert} from "@material-ui/lab";
import {usePage} from "@inertiajs/inertia-react";
import {Divider} from "@material-ui/core";
import Modal from "@/Components/Modal";
import {Inertia} from "@inertiajs/inertia";

export default function PlanExpenses({ planId }) {
    const { userPendingTransactions, planMemberList, allUserExpenses, errors } = usePage().props;
    const [ openPaymentModal, setOpenPaymentModal ] = useState(false);
    const [ selectedUserEmail, setSelectedUserEmail ] = useState('');
    const [ paymentAmount, setPaymentAmount ] = useState(0);
    const [ userPassword, setUserPassword ] = useState('');
    const [ userPasswordError, setUserPasswordError ] = useState('');
    const [ upiId, setUpiId ] = useState('');
    const [ upiIdError, setUpiIdError ] = useState('');

    useEffect(() => {
        if (errors.password || errors.upiId) {
            setUserPasswordError(errors.password);
            setUpiIdError(errors.upiId);
        } else {
            setUserPasswordError('');
            setUserPassword('');
            setUpiIdError('');
            setUpiId('');
            setOpenPaymentModal(false);
        }
    },[errors]);

    const handlePayment = () => {
        Inertia.post('/settle-dues', {
            destUserEmail: selectedUserEmail,
            amount: paymentAmount,
            password: userPassword,
            planId: planId,
            upiId: upiId
        });
    }

    const handlePayButtonClick = (email, amount) => {
        setSelectedUserEmail(email);
        setPaymentAmount(amount);
        setOpenPaymentModal(true);
    }

    const handleModalOpen = (isOpen) => {
        if (!isOpen) {
            setUserPasswordError('');
            setUserPassword('');
            setUpiIdError('');
            setUpiId('');
        }
        setOpenPaymentModal(isOpen);
    }

    const getFullNameFromEmail = (email) => planMemberList.filter((member) => member.email === email)[0].fullName
    return (
        <div>
            <h3 className="text-2xl mt-7">Your Pending Transactions</h3>
            <p className="text-xs mt-1">Below is table containing the list of transactions you need to perform to settle your dues.<br/>You need to either receive from or give money to the user mentioned in the first column.</p>
            <Divider className="w-2/3" style={{marginTop: '0.75rem', marginBottom: '0.5rem'}} />
            {userPendingTransactions.length > 0 ? (
                <Table headers={['Name', 'You will', 'Amount', 'Pay Now']}>
                    {userPendingTransactions.map((userPendingTransaction, index) => (
                        <tr key={`pending-transaction-${index}`}>
                            <td className="px-6 py-4 whitespace-nowrap">
                                <div className="flex items-center">
                                    <div className="text-sm font-medium text-gray-900">
                                        {getFullNameFromEmail(userPendingTransaction.otherUserEmail)}
                                        <p className="text-xs mt-1">{userPendingTransaction.otherUserEmail}</p>
                                    </div>
                                </div>
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap">
                                <div className="text-sm text-gray-900">
                                    <span className={`inline-flex items-center justify-center px-2 py-1 text-sm font-bold leading-none ${userPendingTransaction.action === 'receive' ? 'text-green-100' : 'text-red-100'} ${userPendingTransaction.action === 'receive' ? 'bg-green-600' : 'bg-red-600'} rounded-full`}>
                                        {userPendingTransaction.action}
                                    </span>
                                </div>
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap">
                                <div className="text-sm text-gray-900 font-semibold">{userPendingTransaction.amount}</div>
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap text-blue-400">
                                {userPendingTransaction.action === 'pay' ? (
                                    <span className="cursor-pointer" onClick={() => handlePayButtonClick(userPendingTransaction.otherUserEmail, userPendingTransaction.amount)}>
                                        <PaymentIcon/>
                                    </span>
                                ) : (
                                    <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                )}
                            </td>
                        </tr>
                    ))}
                </Table>
            ) : (<Alert className="mt-2 max-w-full sm:max-w-3/4" severity="success">Hooray! You have no pending transactions.</Alert>)}
            <h3 className="text-2xl mt-7">Your Past Expenses</h3>
            <Divider className="w-2/3" style={{marginTop: '0.75rem', marginBottom: '0.75rem'}} />
            {allUserExpenses.length > 0 ? (
                <Table headers={['Category', 'Amount', 'Is Shared', 'Share Details', 'Time (IST)']}>
                    {allUserExpenses.map((expense, expIndex) => (
                        <tr key={`expense-item-${expIndex}`}>
                            <td className="px-6 py-4 whitespace-nowrap">
                                <div className="flex items-center">
                                    <div className="text-sm font-medium text-gray-900">
                                        {expense.categoryName}
                                    </div>
                                </div>
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap">
                                <div className="text-sm text-gray-900">
                                    {expense.amount}
                                </div>
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap">
                                <div className="text-sm text-gray-900 font-semibold">
                                    {expense.isShared ? 'Yes' : 'No'}
                                </div>
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap">
                                {expense.isShared ? (
                                    <ul className="text-xs">
                                        {expense.shareDetails.map((detail, index) => (
                                            <li key={`user-who-paid-${index}-expense-item-${expIndex}`}>
                                                {getFullNameFromEmail(detail.userEmailWhoPaid)} paid {detail.amount}
                                            </li>
                                        ))}
                                    </ul>
                                ) : 'N.A.'}
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap">
                                <div className="text-sm text-gray-900 font-semibold">
                                    {expense.createdAt}
                                </div>
                            </td>
                        </tr>
                    ))}
                </Table>
            ) : (<Alert className="mt-2 max-w-full sm:max-w-3/4" severity="info">You have not made any expense in this plan.</Alert>)}
            <Modal title="Settle Due" open={openPaymentModal} setOpen={handleModalOpen} actionText="Proceed To Payment" onClickAction={handlePayment}>
                <div className="mt-4">
                    <p className="block font-medium">
                        <span className="text-gray-700">Email: </span> <span className="font-semibold">{selectedUserEmail}</span>
                    </p>
                    <p className="block font-medium mt-5">
                        <span className="text-gray-700">Full Name: </span> <span className="font-semibold">
                            {selectedUserEmail ? getFullNameFromEmail(selectedUserEmail) : ''}
                    </span>
                    </p>
                    <p className="block font-medium mt-5">
                        <span className="text-gray-700">Amount: </span> <span className="font-semibold">{paymentAmount}</span>
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

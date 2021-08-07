import React from "react";
import Table from "@/Components/Table";
import PaymentIcon from "@material-ui/icons/Payment";
import {Alert} from "@material-ui/lab";
import {usePage} from "@inertiajs/inertia-react";
import {Divider} from "@material-ui/core";

export default function PlanExpenses({ planId }) {
    const { userPendingTransactions, planMemberList, allUserExpenses } = usePage().props;

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
                                    <span className={`inline-flex items-center justify-center px-2 py-1 text-sm font-bold leading-none text-${userPendingTransaction.action === 'receive' ? 'green' : 'red'}-100 bg-${userPendingTransaction.action === 'receive' ? 'green' : 'red'}-600 rounded-full`}>
                                        {userPendingTransaction.action}
                                    </span>
                                </div>
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap">
                                <div className="text-sm text-gray-900 font-semibold">{userPendingTransaction.amount}</div>
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap text-blue-400">
                                {userPendingTransaction.action === 'pay' ? (<span className="cursor-pointer"><PaymentIcon/></span>) : (
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
        </div>
    );
}

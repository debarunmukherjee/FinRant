import React from "react";
import Table from "@/Components/Table";
import PaymentIcon from "@material-ui/icons/Payment";
import {Alert} from "@material-ui/lab";
import {usePage} from "@inertiajs/inertia-react";

export default function PlanExpenses({ planId }) {
    const { userPendingTransactions, planMemberList } = usePage().props;
    return (
        <div>
            <h2 className="font-semibold mb-5 text-xl text-center sm:text-2xl">Your Pending Transactions:</h2>
            {userPendingTransactions.length > 0 ? (
                <Table headers={['Name', 'Action', 'Amount', 'Pay Now']}>
                    {userPendingTransactions.map((userPendingTransaction, index) => (
                        <tr key={`pending-transaction-${index}`}>
                            <td className="px-6 py-4 whitespace-nowrap">
                                <div className="flex items-center">
                                    <div className="ml-4">
                                        <div className="text-sm font-medium text-gray-900">
                                            {planMemberList.filter((member) => member.email === userPendingTransaction.otherUserEmail)[0].fullName}
                                            <p className="text-xs mt-1">{userPendingTransaction.otherUserEmail}</p>
                                        </div>
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
                                <span className="cursor-pointer"><PaymentIcon/></span>
                            </td>
                        </tr>
                    ))}
                </Table>
            ) : (<Alert className="mt-2" severity="success">Hooray! You have no pending transactions.</Alert>)}
        </div>
    );
}

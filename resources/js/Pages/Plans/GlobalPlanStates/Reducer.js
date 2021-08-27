export const initialRecordExpenseState = {
    isSharedExpense: false,
    selectedExpenseCategory: null,
    expenseAmount: 0,
    sharedExpenseMembersWhoPaid: [],
    sharedExpenseMemberToBeAdded: {},
    sharedExpenseMemberAmount: 0,
    sharedExpenseMembersPaidEqually: true,
    sharedMemberExpenseError: '',
    sharedExpenseMembersDistributedEqually: true,
    sharedExpenseMembersDistribution : [],
    sharedExpenseDistributionMemberToBeAdded : {},
    sharedExpenseMemberDistributionAmount: 0,
    sharedMemberDistributionError: '',
    shouldResetAllValues: false
}

export const rootReducer = (state, updatedData) => {
    return {
        ...state,
        ...updatedData
    }
}

import React, {useEffect, useState} from "react";
import {Divider, Grid, List, ListItem, ListItemText} from "@material-ui/core";
import Button from "@/Components/Button";
import {Inertia} from "@inertiajs/inertia";
import {usePage} from "@inertiajs/inertia-react";
import AccountBalanceWalletIcon from '@material-ui/icons/AccountBalanceWallet';
import Modal from "@/Components/Modal";
import {Alert} from "@material-ui/lab";
import BudgetList from "@/Pages/Plans/BudgetList";
import AutocompleteSelect from "@/Components/AutocompleteSelect";
import {ResponsiveContainer, BarChart, Bar, CartesianGrid, Tooltip, Legend, XAxis, YAxis} from "recharts";
import {makeStyles} from "@material-ui/core/styles";

const useStyles = makeStyles((theme) => ({
    root: {
        padding: '1rem'
    },
}));

export default function PlanDetails({ id }) {
    const { errors, categoryList, budgetList, planBudgetExpenseData } = usePage().props;
    const [newCategory, setNewCategory] = useState('');
    const [openBudgetAddModal, setOpenBudgetAddModal] = useState(false);
    const [openBudgetEditModal, setOpenBudgetEditModal] = useState(false);
    const [openBudgetDeleteModal, setOpenBudgetDeleteModal] = useState(false);
    const [newBudgetAmount, setNewBudgetAmount] = useState(0);
    const [newBudgetCategory, setNewBudgetCategory] = useState(categoryList.length > 0 ? categoryList[0] : null);
    const [selectedBudgetItemIndex, setSelectedBudgeItemIndex] = useState(0);
    const [editedBudgetAmount, setEditedBudgetAmount] = useState(0);
    const [selectedBudgetItemToBeDeleted, setSelectedBudgetItemToBeDeleted] = useState(0);

    const classes = useStyles();

    useEffect(() => {
        if (!errors.categoryName) {
            setNewCategory('');
        }
        if (!errors.budgetCategoryName && !errors.amount) {
            setNewBudgetAmount(0);
            setNewBudgetCategory(categoryList.length > 0 ? categoryList[0] : null);
            setOpenBudgetAddModal(false);
        }
        if (!errors.editAmount) {
            setEditedBudgetAmount(0);
            setOpenBudgetEditModal(false);
        }
        setOpenBudgetDeleteModal(false);
    }, [errors])

    const handleCategoryAdd = () => {
        Inertia.post('/category/add', {
            categoryName: newCategory
        });
    }

    const handleSetBudget = () => {
        Inertia.post('/budget/add', {
            budgetCategoryName: newBudgetCategory.name,
            amount: newBudgetAmount,
            planId: id
        });
    }

    const handleEditBudget = () => {
        Inertia.put('/budget/edit', {
            budgetCategoryName: budgetList[selectedBudgetItemIndex].name,
            editAmount: editedBudgetAmount,
            planId: id
        });
    }

    const handleDeleteBudget = () => {
        Inertia.post('/budget/delete', {
            budgetCategoryName: budgetList[selectedBudgetItemToBeDeleted].name,
            planId: id
        });
    }

    const handleBudgetEditClick = (index) => {
        setSelectedBudgeItemIndex(index);
        setEditedBudgetAmount(budgetList[index].amount);
        setOpenBudgetEditModal(true);
    }

    const handleBudgetDeleteClick = (index) => {
        setSelectedBudgetItemToBeDeleted(index);
        setOpenBudgetDeleteModal(true);
    }

    return (
        <>
            <Grid container spacing={2}>
                <Grid item xs={12} sm={6}>
                    <div className="flex sm:flex-row flex-col sm:space-x-4 space-x-0 space-y-4 sm:space-y-0">
                        <input
                            type="text"
                            className="w-auto focus:ring-indigo-500 focus:border-indigo-500 flex-1 w-auto inline rounded sm:text-sm border-gray-300"
                            placeholder="New expense category"
                            value={newCategory}
                            onChange={(e) => {setNewCategory(e.target.value)}}
                        />
                    <Button className="hover:bg-blue-500 bg-blue-400 inline" onClick={handleCategoryAdd}>
                            Add
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </Button>
                    </div>
                    {errors.categoryName ? (<p className="text-red-500 text-xs mt-1">{errors.categoryName}</p>) : ''}
                    <div className="mt-12">
                        <h2 className="font-semibold mb-5 text-xl text-center sm:text-2xl">Available Categories:</h2>
                        {categoryList.length > 0 ? (
                            <div className="shadow-md mx-auto w-full rounded">
                                <div className='shadow-lg mb-4 p-3'>
                                    <p className="text-xl font-semibold">Your Categories</p>
                                </div>
                                <List
                                    component="nav"
                                    aria-labelledby="categories"
                                    className={`${classes.root} max-h-20-rem overflow-y-scroll divide-y divide-gray-200`}
                                >
                                    {categoryList.map((category, index) => (
                                        <ListItem key={index}>
                                            <ListItemText primary={category.name}/>
                                        </ListItem>
                                    ))}
                                </List>
                            </div>
                        ) : (<Alert severity="info">You created any categories.</Alert>)}
                    </div>
                </Grid>
                <Grid item xs={12} sm={6}>
                    <div className="flex sm:flex-row-reverse flex-col">
                        <Button className="hover:bg-blue-500 bg-blue-400 inline justify-center" onClick={() => {setOpenBudgetAddModal(true);}}>
                            Set budget for a category
                            <AccountBalanceWalletIcon/>
                        </Button>
                    </div>
                    <div className="mt-12">
                        <h2 className="font-semibold mb-5 text-xl text-center sm:text-2xl">Your budget list:</h2>
                        <BudgetList budgetList={budgetList} onClickEdit={handleBudgetEditClick} onClickDelete={handleBudgetDeleteClick}/>
                    </div>
                </Grid>
            </Grid>
            <h2 className="font-semibold mb-5 mt-8 text-xl sm:text-2xl">Budget vs Expense</h2>
            <p className="text-xs mt-1">
                Please note that this will only show unshared expenses of the user.<br/>
                As of now we consider every category as user specific and even when they belong to the same plan the categories are not shared.<br/>
                When recording a shared expense we simply fetch the category name set by the user creating the shared expense and use that name for recording expense of every user.<br/>
                We are soon coming up with a feature of shared expense categories in a single plan, so that users will be able to set budgets for these and record expenses separately. But for now, shared expenses even with the same category name will <b>NOT</b> be shown here.
            </p>
            <Divider className="w-2/3" style={{marginTop: '0.75rem', marginBottom: '0.5rem'}} />
            <div className="overflow-auto">
                <div className="h-96 min-w-96 overflow-x-scroll">
                    <ResponsiveContainer>
                        <BarChart
                            width={500}
                            height={300}
                            data={planBudgetExpenseData}
                            margin={{
                                top: 20,
                                right: 30,
                                left: 20,
                                bottom: 5
                            }}
                        >
                            <CartesianGrid strokeDasharray="3 3" />
                            <XAxis dataKey="name" />
                            <YAxis />
                            <Tooltip />
                            <Legend />
                            <Bar isAnimationActive={true} dataKey="expense" stackId="a" fill="#8884d8" />
                            <Bar isAnimationActive={true} dataKey="budget" stackId="a" fill="#82ca9d" />
                        </BarChart>
                    </ResponsiveContainer>
                </div>
            </div>
            <Modal title="Set Budget" open={openBudgetAddModal} setOpen={setOpenBudgetAddModal} actionText="Set Budget" onClickAction={handleSetBudget}>
                <div>
                    <label htmlFor="plan_name" className="block text-sm font-medium text-gray-700">
                        Category
                    </label>
                    <div className="mt-1">
                        {categoryList.length > 0 && newBudgetCategory ? (
                            <AutocompleteSelect
                                itemsList={categoryList}
                                itemLabelKey="name"
                                selectedValue={newBudgetCategory}
                                setSelectedValue={setNewBudgetCategory}
                                placeholder="Search Category"
                            />
                        ) : (<Alert severity="info" className="max-w-full sm:max-w-3/4">Please create a category first!</Alert>)}
                    </div>
                    <p className="mt-2 text-sm text-gray-500">
                        Select the expense category for which you want to set a budget for.
                    </p>
                </div>
                <div className="mt-2">
                    <label htmlFor="budget_amt" className="block text-sm font-medium text-gray-700">
                        Budget Amount
                    </label>
                    <div className="mt-1">
                        <input
                            type="number"
                            id="budget_amt"
                            className="w-auto focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded sm:text-sm border-gray-300"
                            placeholder="Amount"
                            value={Number(newBudgetAmount).toString()}
                            onChange={(e) => {
                                let value = e.target.value;
                                if (value === '') {
                                    value = 0;
                                } else {
                                    value = parseInt(value);
                                }
                                if (value < 0) {
                                    value = 0;
                                }
                                if (Number.isInteger(value)) {
                                    setNewBudgetAmount(value);
                                }
                            }}
                        />
                    </div>
                    <p className="mt-2 text-sm text-gray-500">
                        Set the budget amount
                    </p>
                </div>
                {errors.budgetCategoryName ? <Alert className="mt-2" severity="error">{errors.budgetCategoryName}</Alert> : ''}
                {errors.amount ? <Alert className="mt-2" severity="error">{errors.amount}</Alert> : ''}
            </Modal>
            <Modal title="Edit Budget" open={openBudgetEditModal} setOpen={setOpenBudgetEditModal} actionText="Edit Budget" onClickAction={handleEditBudget}>
                <div className="mt-4">
                    <p className="block text-lg font-medium">
                        <span className="text-gray-700">Category: </span> <span className="font-semibold">{budgetList[selectedBudgetItemIndex] && budgetList[selectedBudgetItemIndex].name}</span>
                    </p>
                </div>
                <div className="mt-2">
                    <label htmlFor="budget_amt" className="block text-sm font-medium text-gray-700">
                        Budget Amount
                    </label>
                    <div className="mt-1">
                        <input
                            type="number"
                            className="w-auto focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded sm:text-sm border-gray-300"
                            placeholder="Amount"
                            value={Number(editedBudgetAmount).toString()}
                            onChange={(e) => {
                                let value = e.target.value;
                                if (value === '') {
                                    value = 0;
                                } else {
                                    value = parseInt(value);
                                }
                                if (value < 0) {
                                    value = 0;
                                }
                                if (Number.isInteger(value)) {
                                    setEditedBudgetAmount(value);
                                }
                            }}
                        />
                    </div>
                    <p className="mt-2 text-sm text-gray-500">
                        Set the budget amount
                    </p>
                </div>
                {errors.editAmount ? <Alert className="mt-2" severity="error">{errors.editAmount}</Alert> : ''}
            </Modal>
            <Modal title="Delete Budget" open={openBudgetDeleteModal} setOpen={setOpenBudgetDeleteModal} actionText="Delete Budget" onClickAction={handleDeleteBudget} isDangerAction={true}>
                <div className="mt-4">
                    <p className="block text-base font-medium">
                        Are you sure you want to delete your budget for <span className="font-bold">{budgetList[selectedBudgetItemToBeDeleted] && budgetList[selectedBudgetItemToBeDeleted].name}</span>?
                    </p>
                </div>
            </Modal>
        </>
    )
}

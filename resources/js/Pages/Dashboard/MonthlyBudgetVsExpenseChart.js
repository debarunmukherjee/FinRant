import React, {useEffect, useState} from "react";
import {
    AreaChart,
    Area,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip, ResponsiveContainer, Legend, Pie, Cell, PieChart
} from "recharts";
import {usePage} from "@inertiajs/inertia-react";
import {getMonthNameFromNumber} from "@/Utils/Common";
import {Grid} from "@material-ui/core";

export default function MonthlyBudgetVsExpenseChart() {
    const { lastFiveMonthExpenses, lastFiveMonthBudget } = usePage().props;
    const [ dataPoints, setDataPoints ] = useState([]);
    const [ currentProgressData, setCurrentProgressData ] = useState([]);

    useEffect(() => {
        const preparedData = [];
        for (let i = 4; i >= 0; i--) {
            preparedData.push({
                name: `${getMonthNameFromNumber(lastFiveMonthExpenses[i].month)}, ${lastFiveMonthExpenses[i].year}`,
                expense: lastFiveMonthExpenses[i].amount,
                budget: lastFiveMonthBudget[i].amount,
            });
        }
        setCurrentProgressData([
            {name: 'Expense', value: preparedData[4].expense},
            {name: 'Budget', value: preparedData[4].budget},
        ]);
        setDataPoints(preparedData);
    }, []);

    return (
        <Grid container>
            <Grid item xs={12} sm={6}>
                <div className="mt-5">
                    <h2 className="font-semibold mb-5 text-xl text-center sm:text-2xl">Your Expenses Chart</h2>
                    <div className="h-64">
                        <ResponsiveContainer>
                            <AreaChart
                                width={500}
                                height={400}
                                data={dataPoints}
                                margin={{
                                    top: 10,
                                    right: 30,
                                    left: 0,
                                    bottom: 0
                                }}
                            >
                                <CartesianGrid strokeDasharray="3 3" />
                                <XAxis dataKey="name" />
                                <YAxis />
                                <Tooltip />
                                <Area
                                    isAnimationActive={true}
                                    type="monotone"
                                    dataKey="expense"
                                    stroke="#FF2E2E"
                                    fill="#FF2E2E"
                                />
                                <Area
                                    isAnimationActive={true}
                                    type="monotone"
                                    dataKey="budget"
                                    stroke="#32CD32"
                                    fill="#32CD32"
                                />
                            </AreaChart>
                        </ResponsiveContainer>
                    </div>
                </div>
            </Grid>
            <Grid item xs={12} sm={6}>
                <div className="mt-5">
                    <h2 className="font-semibold mb-5 text-xl text-center sm:text-2xl">Current Month's Progress</h2>
                    <div className="h-64">
                        <ResponsiveContainer width="100%" height="100%">
                            <PieChart width={800} height={400}>
                                <Pie
                                    data={currentProgressData}
                                    innerRadius={60}
                                    outerRadius={80}
                                    fill="#8884d8"
                                    paddingAngle={5}
                                    dataKey="value"
                                >
                                    {currentProgressData.map((entry, index) => (
                                        <Cell key={`cell-${index}`} fill={entry.name === 'Budget' ? '#00C49F' : '#FF8042'} />
                                    ))}
                                </Pie>
                                <Tooltip filterNull={false} />
                                <Legend />
                            </PieChart>
                        </ResponsiveContainer>
                    </div>
                </div>
            </Grid>
        </Grid>
    );
}

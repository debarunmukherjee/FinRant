import React, {useState} from "react";
import {Grid} from "@material-ui/core";
import Button from "@/Components/Button";
import {Inertia} from "@inertiajs/inertia";
import {usePage} from "@inertiajs/inertia-react";

export default function PlanDetails() {
    const { errors } = usePage().props;
    const [newCategory, setNewCategory] = useState('');

    const handleCategoryAdd = () => {
        Inertia.post('/category/add', {
            name: newCategory
        });
    }

    return (
        <Grid container spacing={2}>
            <Grid item xs={12} sm={6}>
                <div className="flex space-x-4">
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
                {errors.name ? (<p className="text-red-500 text-xs mt-1">{errors.name}</p>) : ''}
            </Grid>
        </Grid>
    )
}

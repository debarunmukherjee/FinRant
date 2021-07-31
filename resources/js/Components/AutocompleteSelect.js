import {Autocomplete} from "@material-ui/lab";
import React, {useState} from "react";

export default function AutocompleteSelect({ itemsList, selectedValue, itemLabelKey, setSelectedValue }) {
    const [value, setValue] = useState(selectedValue);
    const [inputValue, setInputValue] = useState('');
    return (
        <Autocomplete
            options={itemsList}
            getOptionLabel={(option) => (itemLabelKey ? option[itemLabelKey] : option)}
            value={value}
            onChange={(event, newInputValue) => {
                setSelectedValue(itemLabelKey ? newInputValue[itemLabelKey] : newInputValue);
                setValue(newInputValue);
            }}
            inputValue={inputValue}
            onInputChange={(event, newInputValue) => {
                setInputValue(newInputValue);
            }}
            renderInput={(params) => (
                <div ref={params.InputProps.ref} className="flex flex-col relative text-gray-600">
                    <input type="search" placeholder="Search Country" {...params.inputProps} className="bg-white h-10 px-5 pr-10 rounded-full text-sm focus:outline-none"/>
                    <button type="submit"
                            className="absolute right-0 top-0 mt-2 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            )}
        />
    )
}
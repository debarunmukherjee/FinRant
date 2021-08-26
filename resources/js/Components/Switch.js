import { Switch } from '@headlessui/react'
import React from "react";

export default function CustomSwitch({ isEnabled, setIsEnabled, labelText, shouldDisplayYesNo, customClass = '' }) {

    return (
        <div className={`flex ${customClass}`}>
            <Switch.Group>
                <Switch.Label className="mr-4 pt-1.5">
                    <label className="block text-sm font-medium text-gray-700">
                        {labelText} {shouldDisplayYesNo && (isEnabled ? <span className="font-bold">Yes</span> :  <span className="font-bold">No</span>)}
                    </label>
                </Switch.Label>
                <Switch
                    checked={isEnabled}
                    onChange={setIsEnabled}
                    className={`rounded-full shadow h-8 w-20 p-1.5 ${isEnabled ? "bg-blue-100" : "bg-gray-100"} transform transition-colors ease-in-out duration-150 focus:outline-none`}
                >
                <span
                    className={`block rounded-full shadow-lg h-full w-1/2 transform transition ease-in-out duration-150 ${isEnabled ? "bg-blue-500 translate-x-full" : "bg-gray-400 translate-x-0"}`}
                />
                </Switch>
            </Switch.Group>
        </div>
    )
}

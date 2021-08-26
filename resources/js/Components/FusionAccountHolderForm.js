import React, {useState} from 'react';
import Select from "@/Components/Select";
import {Inertia} from "@inertiajs/inertia";
import DatePicker from 'react-date-picker';
import {usePage} from "@inertiajs/inertia-react";

export default function FusionAccountHolderForm() {
    const [gender, setGender] = useState({key: 'Select'});
    const [phone, setPhone] = useState('');
    const [pan, setPan] = useState('');
    const [dob, setDob] = useState(new Date());
    const {errors} = usePage().props;

    const handleSubmit = (e) => {
        e.preventDefault();
        Inertia.post('/create-fusion-account', {
            gender: gender.key === 'Select' ? '' : gender.key,
            phone: phone,
            pan: pan,
            dobYear: dob ? dob.getFullYear() : null,
            dobMonth: dob ? dob.getMonth() : null,
            dobDay: dob ? dob.getDate() : null,
        })
    }

    return (
        <div className="mt-8">
            <form onSubmit={handleSubmit}>
                <div className="shadow sm:rounded-md">
                    <div className="px-4 py-5 bg-white sm:p-6">
                        <div className="grid grid-cols-6 gap-6">
                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="gender" className="block text-sm font-medium text-gray-700">Gender</label>
                                <Select selected={gender} options={[{key: 'Male'}, {key: 'Female'}, {key: 'Other'}]} setSelected={setGender} label='key' noOptionText='Select Gender'/>
                                {errors.gender ? <p className="text-xs text-red-500">{errors.gender}</p> : ''}
                            </div>

                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="phone" className="block text-sm font-medium text-gray-700">Phone Number (10 digit)</label>
                                <input type="number" name="phone" id="phone" autoComplete="tel"
                                       className="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                       onChange={(e) => setPhone(e.target.value)}
                                       value={phone}
                                />
                                {errors.phone ? <p className="text-xs text-red-500">{errors.phone}</p> : ''}
                            </div>

                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="pan" className="block text-sm font-medium text-gray-700">PAN Number</label>
                                <input type="text" name="pan" id="pan" value={pan}
                                       className="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                       onChange={(e) => setPan(e.target.value)}
                                />
                                {errors.pan ? <p className="text-xs text-red-500">{errors.pan}</p> : ''}
                            </div>

                            <div className="col-span-6 sm:col-span-3">
                                <label htmlFor="country" className="block text-sm font-medium text-gray-700">Date Of Birth</label>
                                <DatePicker
                                    onChange={setDob}
                                    value={dob}
                                    className="mt-1 shadow-sm"
                                />
                                {(errors.dobYear || errors.dobMonth || errors.dobDay) ? <p className="text-xs text-red-500">Invalid Date</p> : ''}
                            </div>
                        </div>
                    </div>
                    <div className="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <button type="submit"
                                className="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Create
                        </button>
                    </div>
                </div>
            </form>
        </div>
    );
}

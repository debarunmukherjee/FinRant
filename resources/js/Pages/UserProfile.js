import Authenticated from '@/Layouts/Authenticated';
import React, {useEffect, useState} from 'react';
import {Grid} from "@material-ui/core";
import AutocompleteSelect from "@/Components/AutocompleteSelect";
import ImageUpload from "@/Components/ImageUpload";
import {Inertia} from "@inertiajs/inertia";

export default function Dashboard(props) {
    const { userDetails, countryList, errors } = props;
    const [userFirstName, setUserFirstName] = useState('');
    const [userLastName, setUserLastName] = useState('');
    const [userEmail, setUserEmail] = useState('');
    const [userCountry, setUserCountry] = useState('');
    const [userImageFile, setUserImageFile] = useState(null);
    const [userAvatarUrl, setUserAvatarUrl] = useState('');

    useEffect(() => {
        setUserFirstName(userDetails.firstName);
        setUserLastName(userDetails.lastName);
        setUserEmail(userDetails.email);
        setUserCountry(userDetails.country);
        setUserAvatarUrl(userDetails.avatar);
    }, []);

    const handleSubmit = (e) => {
        e.preventDefault();
        Inertia.post('/user/update', {
            firstName: userFirstName,
            lastName: userLastName,
            email: userEmail,
            avatar: userImageFile,
            country: userCountry
        })
    }

    return (
        <Authenticated
            auth={props.auth}
            errors={props.errors}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Your Profile</h2>}
        >
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <form onSubmit={handleSubmit}>
                                <Grid container>
                                    <Grid item xs={12} sm={6}>
                                        <div className="mb-8 flex flex-col">
                                            <p className="mb-2 font-medium">First Name:</p>
                                            <input
                                                type="text"
                                                className="w-auto focus:ring-indigo-500 focus:border-indigo-500 flex-1 w-auto inline rounded sm:text-sm border-gray-300"
                                                placeholder="New expense category"
                                                value={userFirstName}
                                                onChange={(e) => {setUserFirstName(e.target.value)}}
                                            />
                                            {errors.firstName ? <p className="text-xs text-red-500">{errors.firstName}</p> : ''}
                                        </div>
                                        <div className="mb-8 flex flex-col">
                                            <p className="mb-2 font-medium">Last Name:</p>
                                            <input
                                                type="text"
                                                className="w-auto focus:ring-indigo-500 focus:border-indigo-500 flex-1 w-auto inline rounded sm:text-sm border-gray-300"
                                                placeholder="New expense category"
                                                value={userLastName}
                                                onChange={(e) => {setUserLastName(e.target.value)}}
                                            />
                                            {errors.lastName ? <p className="text-xs text-red-500">{errors.lastName}</p> : ''}
                                        </div>
                                        <div className="mb-8 flex flex-col">
                                            <p className="mb-2 font-medium">Email:</p>
                                            <input
                                                type="email"
                                                className="w-auto focus:ring-indigo-500 focus:border-indigo-500 flex-1 w-auto inline rounded sm:text-sm border-gray-300"
                                                placeholder="New expense category"
                                                value={userEmail}
                                                onChange={(e) => {setUserEmail(e.target.value)}}
                                            />
                                            {errors.email ? <p className="text-xs text-red-500">{errors.email}</p> : ''}
                                        </div>
                                        <div className="mb-8 flex flex-col">
                                            <p className="mb-2 font-medium">Country:</p>
                                            <AutocompleteSelect
                                                itemsList={countryList}
                                                selectedValue={userDetails.country}
                                                setSelectedValue={setUserCountry}
                                            />
                                            {errors.country ? <p className="text-xs text-red-500">{errors.country}</p> : ''}
                                        </div>
                                        <button
                                            type="submit"
                                            className={`w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm`}
                                        >
                                            Save
                                        </button>
                                    </Grid>
                                    <Grid item xs={12} sm={6}>
                                        <div className="flex flex-col justify-center">
                                            <ImageUpload
                                                previewUrl={userAvatarUrl}
                                                setPreviewUrl={setUserAvatarUrl}
                                                setImage={setUserImageFile}
                                                altText={userDetails.firstName}
                                            />
                                            {errors.avatar ? <p className="text-xs text-red-500 text-center mt-3">{errors.avatar}</p> : ''}
                                        </div>
                                    </Grid>
                                </Grid>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </Authenticated>
    );
}
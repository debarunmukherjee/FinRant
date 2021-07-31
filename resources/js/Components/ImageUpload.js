import React, {useEffect, useState} from 'react';
import {makeStyles} from "@material-ui/core/styles";
import {Avatar} from "@material-ui/core";

const useStyles = makeStyles((theme) => ({
    root: {
        display: 'flex',
        '& > *': {
            margin: theme.spacing(1),
        },
    },
    small: {
        width: theme.spacing(7),
        height: theme.spacing(7),
        border: "5px solid rgba(96, 165, 250, 1);"
    },
    large: {
        width: theme.spacing(16),
        height: theme.spacing(16),
        border: "5px solid rgba(96, 165, 250, 1);"
    },
}));

export default function ImageUpload({ previewUrl, setPreviewUrl, setImage, altText })
{
    const [selectedFile, setSelectedFile] = useState();

    useEffect(() => {
        if (!selectedFile) {
            return;
        }

        const objectUrl = URL.createObjectURL(selectedFile);
        setPreviewUrl(objectUrl);

        // free memory when ever this component is unmounted
        return () => URL.revokeObjectURL(objectUrl);
    }, [selectedFile]);

    const classes = useStyles();

    const onSelectFile = e => {
        if (!e.target.files || e.target.files.length === 0) {
            setSelectedFile(undefined);
            return;
        }
        setSelectedFile(e.target.files[0]);
        setImage(e.target.files[0]);
    }

    return (
        <div className="flex flex-col w-full items-center justify-center bg-grey-lighter">
            <Avatar alt={altText} src={previewUrl} className={classes.large} />
            <div className="mt-3">
                <label
                    className="w-64 flex flex-col items-center px-1 py-1 bg-white text-blue-400 rounded-lg shadow-lg tracking-wide uppercase border border-blue-400 cursor-pointer hover:bg-blue-400 hover:text-white">
                    <svg className="w-8 h-8" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path
                            d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z"/>
                    </svg>
                    <span className="mt-2 text-base leading-normal">Upload Image</span>
                    <input type='file' className="hidden" onChange={onSelectFile}/>
                </label>
            </div>
        </div>
    );
}

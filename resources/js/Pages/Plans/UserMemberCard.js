import React from "react";
import {Avatar} from "@material-ui/core";
import {makeStyles} from "@material-ui/core/styles";

const useStyles = makeStyles((theme) => ({
    small: {
        width: theme.spacing(7),
        height: theme.spacing(7),
        border: "5px solid rgba(96, 165, 250, 1);"
    },
    largePurple: {
        width: theme.spacing(16),
        height: theme.spacing(16),
        border: "5px solid rgba(124, 58, 237, 1);"
    },
    largeGreen: {
        width: theme.spacing(16),
        height: theme.spacing(16),
        border: "5px solid rgba(16, 185, 129, 1);"
    },
}));

export default function UserMemberCard({ fullName, profileImgUrl, role }) {
    const classes = useStyles();
    return (
        <div className="flex flex-col m-5">
            <Avatar alt={fullName} src={profileImgUrl} className={role === 'creator' ? classes.largeGreen : classes.largePurple} />
            <p className="text-center">{fullName}</p>
        </div>
    )
}

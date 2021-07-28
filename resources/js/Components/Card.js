import React from 'react';
import { makeStyles } from '@material-ui/core/styles';
import Card from '@material-ui/core/Card';
import CardActions from '@material-ui/core/CardActions';
import CardContent from '@material-ui/core/CardContent';
import Button from './Button';
import Typography from '@material-ui/core/Typography';
import {InertiaLink} from "@inertiajs/inertia-react";

const useStyles = makeStyles({
    root: {
        minWidth: 270,
        margin: 10,
        backgroundColor: "rgba(219, 234, 254, 1)",
    },
    bullet: {
        display: 'inline-block',
        margin: '0 2px',
        transform: 'scale(0.8)',
    },
    title: {
        fontSize: 14,
    },
    pos: {
        marginBottom: 12,
    },
});

export default function CardWithNavButton({title, subTitle, buttonText, body, navLink}) {
    const classes = useStyles();

    return (
        <Card className={classes.root}>
            <CardContent>
                <Typography variant="h5" component="h2">
                    {title}
                </Typography>
                <Typography className={classes.pos} color="textSecondary">
                    {subTitle}
                </Typography>
                <Typography variant="body2" component="p">
                    {body}
                </Typography>
            </CardContent>
            <CardActions>
                <InertiaLink href={navLink}>
                    <Button className="hover:bg-blue-500 bg-blue-400">{buttonText}</Button>
                </InertiaLink>
            </CardActions>
        </Card>
    );
}

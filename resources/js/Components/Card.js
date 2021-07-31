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
        border: "5px solid rgba(219, 234, 254, 1)",
    },
    bullet: {
        display: 'inline-block',
        margin: '0 2px',
        transform: 'scale(0.8)',
    },
    title: {
        fontSize: '1.125rem',
    },
    pos: {
        marginTop: '5px',
        fontSize: '0.75rem',
        lineHeight: '1rem',
        marginBottom: 12,
    },
    body: {
        marginTop: '2rem'
    }
});

export default function CardWithNavButton({title, subTitle, buttonText, body, navLink}) {
    const classes = useStyles();

    return (
        <Card className={classes.root}>
            <CardContent>
                <Typography className={classes.title} variant="h5" component="h2">
                    {title}
                </Typography>
                <Typography className={classes.pos} color="textSecondary">
                    {subTitle}
                </Typography>
                <hr/>
                <Typography className={classes.body} variant="body2" component="p">
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

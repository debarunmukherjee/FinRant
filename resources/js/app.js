import {SnackbarProvider} from "notistack";

require('./bootstrap');

// Import modules...
import React from 'react';
import { render } from 'react-dom';
import { App } from '@inertiajs/inertia-react';
import { InertiaProgress } from '@inertiajs/progress';
import Slide from '@material-ui/core/Slide';

const el = document.getElementById('app');

render(
    (
        <SnackbarProvider maxSnack={1} anchorOrigin={{vertical: 'bottom', horizontal: 'right'}} TransitionComponent={Slide}>
            <App initialPage={JSON.parse(el.dataset.page)} resolveComponent={(name) => require(`./Pages/${name}`).default} />)
        </SnackbarProvider>
    ),
    el
);

InertiaProgress.init({ color: '#4B5563' });

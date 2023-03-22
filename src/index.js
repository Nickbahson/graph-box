import domReady from '@wordpress/dom-ready';
import { render } from '@wordpress/element';
import { __ } from '@wordpress/i18n'
import { SelectControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

import { useState, useEffect } from '@wordpress/element';

import { LineChart, Line, CartesianGrid, XAxis, YAxis } from 'recharts';

const RenderLineChart = () => {
    const data = [
        {name: 'Page A', uv: 400, pv: 2400, amt: 2400},
        {name: 'Page B', uv: 500, pv: 2800, amt: 2900},
        {name: 'Page C', uv: 450, pv: 1400, amt: 6400},
        {name: 'Page D', uv: 600, pv: 5400, amt: 3500},
        {name: 'Page W', uv: 800, pv: 4400, amt: 1800},
        {name: 'Page Z', uv: 700, pv: 3400, amt: 4800},
    ];

    return (
        <LineChart width={600} height={300} data={data} margin={{ top: 5, right: 20, bottom: 5, left: 0 }}>
            <Line type="monotone" dataKey="uv" stroke="#8884d8" />
            <CartesianGrid stroke="#ccc" strokeDasharray="5 5" />
            <XAxis dataKey="name" />
            <YAxis />
        </LineChart>
    )
}
domReady( function () {
    const htmlOutput = document.getElementById(
        'graph-box-wrapper'
    );



    const SelectDays = () => {

        const [ type, setDays ] = useState('7');



        return (
            <>
                <SelectControl
                    label={__('Product Type', 'flair-store')}
                    value={ type }
                    options={ [
                        { label: '7 days', value: '7' },
                        { label: '14 days', value: '14' },
                        { label: '30 days', value: '30' },
                    ] }
                    onChange={ ( value ) => setDays( value ) }
                    __nextHasNoMarginBottom
                />
                <RenderLineChart/>
            </>
        )
    }


    if ( htmlOutput ) {
        render( <SelectDays/>, htmlOutput );
    }
} );

console.log("HERE nOW")
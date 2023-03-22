import domReady from '@wordpress/dom-ready';
import { render } from '@wordpress/element';
import { __ } from '@wordpress/i18n'
import { SelectControl } from '@wordpress/components';

import { useState, useEffect } from '@wordpress/element';

import { LineChart, Line, CartesianGrid, XAxis, YAxis } from 'recharts';

const RenderLineChart = ({data}) => {

    return (
        <LineChart width={600} height={400} data={data} margin={{ top: 5, right: 20, bottom: 5, left: 0 }}>
            <Line type="monotone" dataKey="total_amount" stroke="#8884d8" />
            <CartesianGrid stroke="#ccc" strokeDasharray="5 5" />
            <XAxis dataKey="period_start_date" />
            <YAxis />
        </LineChart>
    )
}
domReady( function () {
    const htmlOutput = document.getElementById(
        'graph-box-wrapper'
    );



    const SelectDays = () => {

        const [ selectedDays, setDays ] = useState('7');
        const [ data, setData ] = useState([]);

        const headers = {
            'content-type': 'application/json',
            'X-WP-Nonce': wpApiSettings.nonce
        }

        useEffect(() => {

            fetch(graphBoxData.rest_url,{
                credentials: 'include',
                headers
            })
                .then((res) => res.json())
                .then((data) => {
                    console.log("||||||||||||||||| data ||||||||||||||||")
                    console.log("||||||||||||||||| data ||||||||||||||||")
                    console.log(data)
                    setData(data)
                    console.log("||||||||||||||||| data ||||||||||||||||")
                    console.log("||||||||||||||||| data ||||||||||||||||")
                })
                .catch((err) => console.warn(err))

        }, [selectedDays])



        return (
            <>
                <SelectControl
                    label={__('Days', 'graph-box')}
                    value={ selectedDays }
                    options={ [
                        { label: '7 days', value: '7' },
                        { label: '15 days', value: '15' },
                        { label: '1 Month', value: '30' },
                    ] }
                    onChange={ ( value ) => setDays( value ) }
                    __nextHasNoMarginBottom
                />
                <RenderLineChart data={data}/>
            </>
        )
    }


    if ( htmlOutput ) {
        render( <SelectDays/>, htmlOutput );
    }
} );

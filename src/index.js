import domReady from '@wordpress/dom-ready';
import { __ } from '@wordpress/i18n';
import {
	SelectControl,
	Flex,
	FlexItem,
	Card,
	CardBody,
} from '@wordpress/components';

import { render, useState, useEffect } from '@wordpress/element';

import { BarChart, Bar, CartesianGrid, XAxis, YAxis, Tooltip } from 'recharts';

const RenderChart = ( { data } ) => {
	return (
		<BarChart width={ 350 } height={ 300 } data={ data }>
			<XAxis dataKey="period_start_date" stroke="#8884d8" />
			<YAxis />
			<Tooltip />
			<CartesianGrid stroke="#ccc" strokeDasharray="5 5" />
			<Bar dataKey="total_amount" fill="#8884d8" barSize={ 20 } />
		</BarChart>
	);
};
domReady( function () {
	const htmlOutput = document.getElementById( 'graph-box-wrapper' );

	const App = () => {
		const [ selectedDays, setDays ] = useState( '7' );
		const [ data, setData ] = useState( [] );

		const headers = {
			'content-type': 'application/json',
			'X-WP-Nonce': wpApiSettings.nonce,
		};

		const dataUrl = `${ graphBoxData.rest_url }?days=${ selectedDays }`;

		useEffect( () => {
			fetch( dataUrl, {
				credentials: 'include',
				headers,
			} )
				.then( ( res ) => res.json() )
				.then( ( newData ) => {
					setData( newData );
				} )
				.catch( ( err ) => console.warn( err ) );
		}, [ selectedDays ] );

		return (
			<>
				<Card size="small">
					<CardBody>
						<Flex>
							<FlexItem>
								<h3>{ __( 'Graph Widget', 'graph-box' ) }</h3>
							</FlexItem>
							<FlexItem>
								<SelectControl
									label={ __( 'Days', 'graph-box' ) }
									value={ selectedDays }
									options={ [
										{ label: '7 days', value: '7' },
										{ label: '15 days', value: '15' },
										{ label: '1 Month', value: '30' },
									] }
									onChange={ ( value ) => setDays( value ) }
									__nextHasNoMarginBottom
								/>
							</FlexItem>
						</Flex>
					</CardBody>
					<CardBody>
						<RenderChart data={ data } />
					</CardBody>
				</Card>
			</>
		);
	};

	if ( htmlOutput ) {
		render( <App />, htmlOutput );
	}
} );

const PROXY_URL = 'https://cors-anywhere.herokuapp.com/';
const API_HOST = PROXY_URL + 'https://grupo-aaa-asesores.apifacturacionelectronica.xyz';   //'http://31.220.49.57:8000';
const TEST_SET_ID = 'f5740ab8-aa16-463a-98b9-f7e9348d3535';
const INVOICE_TEST_URL = API_HOST + '/api/ubl2.1/invoice/' + TEST_SET_ID;
const INVOICE_PROD_URL = API_HOST + '/api/ubl2.1/invoice';
const COMPANY_CONFIG_URL = API_HOST + '/api/ubl2.1/config';
const TABLE_MUNICIPALITIES_URL = API_HOST + '/api/ubl2.1/listings?tables=municipalities';
const CREDIT_NOTE_TEST_URL = API_HOST + '/api/ubl2.1/credit-note/' + TEST_SET_ID;
const CREDIT_NOTE_PROD_URL = API_HOST + '/api/ubl2.1/credit-note';
const DEBIT_NOTE_TEST_URL = API_HOST + '/api/ubl2.1/debit-note/' + TEST_SET_ID;
const DEBIT_NOTE_PROD_URL = API_HOST + '/api/ubl2.1/debit-note';
const STATUS_ZIP_URL = API_HOST + '/api/ubl2.1/status/zip/';
const LOGS_FACT = API_HOST + '/api/ubl2.1/logs/';
const XML_DOWNLOAD_URL = '/api/ubl2.1/download-xml';
const SEND_MAIL_URL = '/informes/inf_factura_electronica.php/';
const AUTHORIZATION_TOKEN = 'Naj3GoW49EtDPOBo9TWfxLK3M0lPeBARPz17jzaXVjyLm4PJvDJNvwCX0CbxIY5u0OcTMGrZLaabBVTv';
const BILL_QUERIES_URL = 'consultasFacturacion/consultasFacturacionElectronica.php';

let mdl_info = $('.mdl-info'),
	mdl_title = mdl_info.find('.modal-title'),
	mdl_body = mdl_info.find('.modal-body');

facturacion = {
	events: function(){
		$('.sendBill').on('click', function(){
				let factura = $(this).data('id'),
				row = $(this).parents('tr'),
				button = $(this);            
			button.attr('disabled', true);

            /*fetch(BILL_QUERIES_URL , {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-Requested-With': 'XMLHttpRequest'
				},
				body: JSON.stringify({
					nit: nit,
					type: 'config'
				})
			}).then((response) => {						
				return response.json();
			}).then((company) => {				
				//facturacion.sendBill(bill_data, row, factura, button);
			});*/


			fetch(BILL_QUERIES_URL , {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-Requested-With': 'XMLHttpRequest'
				},
				body: JSON.stringify({
					factura: factura,
					type: 'invoice'
				})
			}).then((response) => {						
				return response.json();
			}).then((bill_data) => {	
			    jsShowWindowLoad('Enviando Factura Electrónica... <br>Un momento por favor.');			
				facturacion.sendBill(bill_data, row, factura, button);
			});
		});

		$(".downloadXML").on('click', function(){
			let zip_id = $(this).data('zip'),
				button = $(this);
			button.attr('disabled', true);
			fetch(STATUS_ZIP_URL + zip_id, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'Authorization':'Bearer '+AUTHORIZATION_TOKEN,
					'X-Requested-With': 'XMLHttpRequest',
					'Access-Control-Allow-Origin': "*",
					"Access-Control-Allow-Credentials": "true"
				}
			})
			.then((response) => {
				return response.json();
			}).then((json) => {
				const data = new Blob([json.content], { type: 'xml' });
				const url = window.URL.createObjectURL(data);
				const a = document.createElement('a');
				a.style.display = 'none';
				a.href = url;
				a.download = json.fileName;
				document.body.appendChild(a);
				a.click();
				window.URL.revokeObjectURL(url);
				button.removeAttr('disabled');
			});
		});

		$('.sendMail').on('click', function(){
			let factura = $(this).data('factura'),
				btn = $(this);
			btn.attr('disabled', true);
			fetch(SEND_MAIL_URL + factura, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-Requested-With': 'XMLHttpRequest'
				},
				body: JSON.stringify({ mail: true })
			}).then((response) => {
				return response.text();
			}).then((json) => {
				console.log(json);
				/*let status = parseInt(json.statusCode),
					message = json.message, title = '';

				if(status === 200) title = 'Información';
				else title = 'Advertencia';

				mdl_title.html(title);
				mdl_body.html(message);
				mdl_info.modal('show');
				btn.removeAttr('disabled');*/
			});
		});
	},

	sendCompany : function(COMPANIA){
    	alert('holaaaa :');
      	fetch(COMPANY_CONFIG_URL , {
			method: 'PUT',
			headers: {
				'Accept': 'application/json',
				'Content-Type': 'application/json',
				'Authorization': 'Bearer '+AUTHORIZATION_TOKEN,
			},
			body: JSON.stringify(COMPANIA)
		}).then((response) => {
			return response.json();
		}).then((json) => {
	 });
	},

    sendSoftware : function(){

    },


    sendResolution : function(){

    },

	sendBill: function(INVOICE_DATA, TABLE_ROW, BILL_ID, BUTTON){
		fetch(INVOICE_PROD_URL , {
			method: 'POST',
			headers: {
				'Accept': 'application/json',
				'Content-Type': 'application/json',
				'Authorization': 'Bearer '+AUTHORIZATION_TOKEN,
			},
			body: JSON.stringify(INVOICE_DATA)
		}).then((response) => {
			return response.json();
		}).then((json) => {
			if(json.errors){
				if(Object.keys(json.errors).length > 0){
					jsRemoveWindowLoad();
					let errors = '';
					for(let i = 0; i < Object.keys(json.errors).length; i++){
						errors += '<p>'+Object.values(json.errors)[i][0]+'</p>';
					}
					mdl_title.html('Advertencia');
					mdl_body.html(errors);
					mdl_info.modal('show');
					BUTTON.removeAttr('disabled');
				}
			}else{
				let zip_name = json.zip_name,
				    cufe = json.uuid,				    
				    statusCode = json.status_code,
				    errorMessages = json.errors_messages,
				    statusMessages = json.status_description,
				    warnings = '',	
				    messages = '',		   
				    data = json.qr_data,  
				    xml = json.attached_document_base64_bytes,
				    pdf = json.pdf_base64_bytes;				         
                //respuesta del servidor DIAN
                	if(statusCode === "00"){           		
                      
						messages = '<p>'+statusMessages+'</p>';						
						mdl_title.html('Información');
						mdl_body.html(messages);
						mdl_info.modal('show');
						BUTTON.removeAttr('disabled');					                      
                        //se envían los datos para ser guardados en la base de datos                        
						facturacion.billUpdate(data, xml, pdf, BILL_ID, cufe, zip_name, TABLE_ROW);
						jsRemoveWindowLoad();
					}else if(statusCode === "66"){
						jsRemoveWindowLoad();
						mdl_title.html('Advertencia');
						mdl_body.html('TrackId no existe en los registros de la DIAN.');
						mdl_info.modal('show');
						BUTTON.removeAttr('disabled');
					}else if(parseInt(statusCode) === 0){
						jsRemoveWindowLoad();
						mdl_title.html('Error');
						mdl_body.html('Ocurrió un error al momento de procesar su solicitud');
						mdl_info.modal('show');
						BUTTON.removeAttr('disabled');
					}else{	
					jsRemoveWindowLoad();					
						for(let i = 0; i < errorMessages.length; i++){
							warnings += '<p>'+errorMessages[i]+'</p>';
						}
						mdl_title.html('Advertencia');
						mdl_body.html(warnings);
						mdl_info.modal('show');
						BUTTON.removeAttr('disabled');
					}					


				/*fetch(LOGS_FACT + cufe_fact, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'Authorization': 'Bearer ' +AUTHORIZATION_TOKEN,
						'X-Requested-With': 'XMLHttpRequest'
					}
				}).then((resp) => {
					return resp.json();
				}).then((jsn) => {					
					let statusCode = jsn.statusCode.toString(),
						errorMessages = jsn.error,										 
						data = jsn.billData, warnings = '';  
						console.log('data:'+data);
						console.log('error:'+errorMessages);
						console.log('codigo:'+statusCode);
					if(statusCode === "00"){
						facturacion.billUpdate(data, BILL_ID, zip_code, TABLE_ROW);
					}else if(statusCode === "66"){
						mdl_title.html('Advertencia');
						mdl_body.html('TrackId no existe en los registros de la DIAN.');
						mdl_info.modal('show');
						BUTTON.removeAttr('disabled');
					}else if(parseInt(jsn.statusCode) === 0){
						mdl_title.html('Error');
						mdl_body.html('Ocurrio un error al momento de procesar tu solicitud');
						mdl_info.modal('show');
						BUTTON.removeAttr('disabled');
					}else{
						for(let i = 0; i < errorMessages.string.length; i++){
							warnings += '<p>'+errorMessages.string[i]+'</p>';
						}
						mdl_title.html('Advertencia');
						mdl_body.html(warnings);
						mdl_info.modal('show');
						BUTTON.removeAttr('disabled');
					}					
				});*/
			}
		});
	},
	billUpdate: function(DATA, XML, PDF, BILL_ID, CUFE, ZIP_NAME, TABLE_ROW){		
		console.log(DATA, CUFE);
		fetch(BILL_QUERIES_URL, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-Requested-With': 'XMLHttpRequest'
			},
			body: JSON.stringify({ 
				type: 'updateBill', 
				factura: BILL_ID, 
				cufe: CUFE,
				zip_name: ZIP_NAME,
				data: DATA,
				xml: XML,
				pdf: PDF
			})
		}).then((response) => {
			console.log(response);
			return response.json();			
		}).then((json) => {
		  if(parseInt(json.status) == 200){
		  	    jsRemoveWindowLoad();
				TABLE_ROW.remove();
				mdl_title.html('Información');
				mdl_body.html('Factura Procesada Correctamente');
				mdl_info.modal('show');
			}else{
				jsRemoveWindowLoad();
				mdl_title.html('Error');
				mdl_body.html('Ocurrió un error al momento de procesar su solicitud');
				mdl_info.modal('show');
			}
		});
	}
};
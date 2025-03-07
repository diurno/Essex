/*------------------------ 
Backend related javascript
------------------------*/

window.addEventListener('load', (event) => {

    // FUNCTION TO BIND AJAX CREATED ELEMENTS
    const on = (element, event, selector, handler) => {
        element.addEventListener(event, e => {
            if (e.target.closest(selector)) {
                handler(e);
            }
        });
    }

    function serializeArray (array, name) {
        var serialized = '';
        for(var i = 0, j = array.length; i < j; i++) {
            if(i>0) serialized += '&';
            serialized += name + '=' + array[i];
        }
        return serialized;
    }

    function serialize (data) {
        let obj = {};
        for (let [key, value] of data) {
            if (obj[key] !== undefined) {
                if (!Array.isArray(obj[key])) {
                    obj[key] = [obj[key]];
                }
                obj[key].push(value);
            } else {
                obj[key] = value;
            }
        }
        return obj;
    }

    const saveData = (e) => {

        e.preventDefault();

        const form_url = esendex_url.siteurl;
        
        const formElement = document.querySelector('#esendex-salesforce-form'),
        formBodyData = new FormData(formElement),
        actionForm = `${form_url}/wp-json/esendex-us-form-submissions-api/v1/form-submission`;
        

        const ESjsonData = serialize(formBodyData);   

        const fetchAttributes = {
            method: 'POST',    
            headers: {
                'Content-Type': 'application/json'
            },        
            body: JSON.stringify(ESjsonData)
        };

        const displayError = (err) => {
            const errorContainer = document.querySelector('.form-row-error');
            errorContainer.innerHTML = err;
        }
    
        
        const sendDataToSift = () => {
            return new Promise( (resolve, reject) => {
                fetch(actionForm, fetchAttributes).then(response => {
                    if(response.status == 400){
                        return response.json().then(response => {throw new Error(response.message)})
                    }
                    // if status 200 call sendDataToEsendex
                    return response.json()
                }).then( data => {
                    resolve(data)
                }).catch( error => {
                    reject(error)
                })
            });
        }  


        sendDataToSift().then( data => {
            console.log("data===>:" , data);
            window.location.href = 'https://esendex.us/signup/success/';
        }).catch( error => {
            displayError(error);
            console.log("err:", error)
        })

    };


    const formElement = document.querySelector('#esendex-salesforce-form')
    formElement.addEventListener('submit', (event) => {
        saveData(event);
    })

    on(document, 'click', '#ssssend-info', e => {
        saveData(e)
    });

   
    

});

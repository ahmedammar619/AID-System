
document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.edit-btn');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const parentId = button.parentNode.id;
            const text =document.querySelector(`#${parentId} p`)
            const input = document.querySelector(`#${parentId} input`)
            
            text.style.display = 'none';
            input.style.display = 'block';
            // input.value = text.innerHTML;
            input.focus();
            button.innerHTML = 'Submit';

            button.addEventListener('click', async function() {
                if(confirm('Are you sure you want to change this information?')){
                    if(parentId == 'edit-address'){
                        const addressInput = document.querySelector('#edit-address input');
                        const address = addressInput.value;
                        let userInput = address + ', Texas' ; //default to Texas
                        console.log(userInput);
                        const apiKey = 'OPEN_CAGE_API_KEY'; // Replace with your OpenCage API key
                        fetch(`https://api.opencagedata.com/geocode/v1/json?q=${userInput}&key=${apiKey}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.results.length > 0) {                
                                    var lat = data.results[0].geometry.lat;
                                    var lng = data.results[0].geometry.lng;
                                    window.location.href = 'php/edit_recipient.php?for='+input.name+'&value='+address+'&lat='+lat+'&lng='+lng;
                                }else{
                                    alert('Please enter a valid address or city. Or contact us for help.');
                                }
                            });
                            // window.location.href = 'php/edit_recipient.php?for='+input.name+'&value='+coords;
                    }else{
                        window.location.href = 'php/edit_recipient.php?for='+input.name+'&value='+input.value;
                    }
                }
            });
           
        }, {once : true});
    });
});
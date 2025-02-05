


// location of the user
let userLocation = JSON.parse(sessionStorage.getItem('userLocation'));

if (userLocation) {
    userLocation = userLocation.value;
    document.querySelector('#recipient-header').style.display = 'block';
} 
else{
    document.querySelector('#recipient-header').style.display = 'block';
    userLocation = { lat: 32.9481789, lng: -96.7297206 };
}
async function initMap(recipients, collectLocation) {
    // Set the zoom level of the map
    const centerLat = parseFloat(localStorage.getItem('mapCenterLat'));
    const centerLng = parseFloat(localStorage.getItem('mapCenterLng'));
    const zoom = parseInt(localStorage.getItem('mapZoom'));

    let map;
    if(zoom){
        map = L.map('map',{zoomControl: false}).setView([centerLat, centerLng], zoom);
    }else{
        map = L.map('map',{
            zoomControl: false // disable the default zoom control
        }).setView([userLocation.lat, userLocation.lng], 13); // Set the initial position and zoom level
    }

    // Custom zoom control
    L.control.zoom({
        position: 'bottomright'
    }).addTo(map);

    // Add a custom marker icon for user
    const userIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        shadowSize: [41, 41],
        iconAnchor: [12, 41],
        shadowAnchor: [4, 62],
        popupAnchor: [1, -34]
    });

    // Load the map tiles from OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: 'Map data © <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add a marker for the user's location
    const userMarker = L.marker([userLocation.lat, userLocation.lng], {icon: userIcon}).addTo(map);
    userMarker.bindTooltip("You are here", { permanent: false, direction: "top" });

    if(collectLocation) {
        collectLocation.forEach(collectLocation => {
            const collectLocationIcon = L.divIcon({
                className: 'custom-icon',
                iconSize: [25, 41],
                html: `<svg height="50" width="50">
                        <circle cx="25" cy="25" r="20" stroke="black" stroke-width="3" fill="yellow" />
                       </svg>`
            });
            const collectLocationMarker = L.marker([collectLocation.lat, collectLocation.lng], {icon: collectLocationIcon}).addTo(map);
            collectLocationMarker.bindTooltip("Collect Location", { permanent: false, direction: "top" });
        });
    }

    const recipientHeaderText = document.getElementById('recipient-header');
    recipientHeaderText.innerHTML = `<span style="color:rgb(0, 123, 255)">Total Recipients: ${recipients.length}</span>`;

    let minDistance = 10000000;
    let totalItems = 0;
    let selectedRecipients = [];
    let confirmedRecipients = [];
    let spawnedRecipientMarkers = [];

    // Create a Supercluster instance with fixed geographic distance
    const cluster = new Supercluster({
        radius: 40, // This will stay fixed but we will use fixed distance in miles for clustering
        maxZoom: 16,
        minZoom: 0
    });

    let recipientPoints = recipients.map(recipient => ({
        type: 'Feature',
        properties: {
            cluster: false,
            recipientId: recipient.id,
            items: recipient.items,
            address: recipient.address,
            distance: recipient.distance,
        },
        geometry: {
            type: 'Point',
            coordinates: [recipient.lng, recipient.lat],
        },
    }));

    cluster.load(recipientPoints);

    // Function to calculate distance in miles using the Haversine formula
    function haversineDistance(lat1, lon1, lat2, lon2) {
        const toRad = x => x * Math.PI / 180;
        const R = 3959; // Radius of Earth in miles
        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);
        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                  Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                  Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    function updateClusters() {
        const bounds = map.getBounds();
        const bbox = [
            bounds.getWest(), bounds.getSouth(),
            bounds.getEast(), bounds.getNorth()
        ];

        const clusters = cluster.getClusters(bbox, 16); // Keep zoom level constant for clustering

        // Remove old markers
        spawnedRecipientMarkers.forEach(marker => map.removeLayer(marker));
        spawnedRecipientMarkers = [];

        const clusteredRecipients = [];

        recipients.forEach(recipient => {
            if(recipient.status == 'Confirmed') confirmedRecipients.push(recipient);
            //get recipient distance
            const userLatLng = L.latLng(userLocation.lat, userLocation.lng);
            const recipientLatLng = L.latLng(recipient.lat, recipient.lng);    
            //make to mile not km
            let distance = userLatLng.distanceTo(recipientLatLng) / 1000 * 0.621371;
            recipient.distance = distance.toFixed(1); // convert to string only when displaying
            if (distance < minDistance) {
                minDistance = distance;
            }

            let isClustered = false;

            for (let i = 0; i < clusteredRecipients.length; i++) {
                const clusteredRecipient = clusteredRecipients[i];
                const distance = haversineDistance(recipient.lat, recipient.lng, clusteredRecipient.lat, clusteredRecipient.lng);

                // Cluster recipients within 0.2 miles
                if (distance <= 0.05) {
                    clusteredRecipient.recipients.push(recipient);
                    isClustered = true;
                    break;
                }
            }

            if (!isClustered) {
                clusteredRecipients.push({
                    lat: recipient.lat,
                    lng: recipient.lng,
                    recipients: [recipient]
                });
            }
        });

        // Create new markers for clusters
        clusteredRecipients.forEach(cluster => {
            const [lng, lat] = [cluster.lng, cluster.lat];

            let marker;

            // Bind popup with recipient details
            let isMouseOver = false;
            // Create a div element for the
            const recipientDiv = document.createElement('div');
            recipientDiv.classList.add('recipient-marker-div');
            recipientDiv.style.display = 'none';
            
            
            // Create a div element for the distance
            // let recipientDistanceDiv = document.createElement('div');
            // Create a div element for the address
            // let recipientAddressDiv = document.createElement('div')



    // Loop through each recipient in the cluster
    cluster.recipients.forEach((recipient, index) => {
        // Create a container for each recipient's details
        const recipientDetailsDiv = document.createElement('div');
        recipientDetailsDiv.classList.add('recipient-details');

        const recipientData = document.createElement('div');
        const box = recipient.items > 1 ? `${recipient.items} Boxes` : `${recipient.items} Box`;
        recipientData.innerText = `${recipient.name} | ${recipient.address} | ${box} | ${recipient.distance} miles (${Math.ceil(recipient.distance*2+1)} minutes) away`;
        recipientData.style.marginBottom = '5px';
        recipientDetailsDiv.appendChild(recipientData);

        //add delete button
        const recipientDeleteBtn = document.createElement('button');
        recipientDeleteBtn.classList.add('general-button');
        recipientDeleteBtn.style.display = 'none';
        recipientDeleteBtn.style.backgroundColor = 'red';
        recipientDeleteBtn.innerText = 'Unselect';
        recipientDeleteBtn.addEventListener('click', ()=>{
            const volunteerName = document.getElementById('volunteerName').value;
            if(confirm(`Are you sure you want to deselect recipient ${recipient.name} from the volunteer ${volunteerName}?`))
                window.location.href = "php/select_recipient.php?recipient_id="+recipient.id+"&for=deselect";
        }); 

        // Add a "Select" button for each recipient
        const recipientSelectBtn = document.createElement('button');
        recipientSelectBtn.classList.add('general-button');
        recipientSelectBtn.innerText = 'Select';
        recipientSelectBtn.addEventListener('click', () => {
            window.location.href = `php/select_recipient.php?recipient_id=${recipient.id}&for=select`;
        });

        // Append the button to the recipient details
        recipientDetailsDiv.appendChild(recipientSelectBtn);
        recipientDetailsDiv.appendChild(recipientDeleteBtn);

        // Append the recipient details to the main popup div
        recipientDiv.appendChild(recipientDetailsDiv);

        // Add a horizontal line between recipients
        if(cluster.recipients.length > 1 && (index < cluster.recipients.length - 1) ) recipientDiv.appendChild(document.createElement('hr'));

        function selectRecipient(){
            let coloredIcon;
            if(recipient.volunteer_id == volunteerID) {
                selectedRecipients.push(recipient);   
                totalItems += parseInt(recipient.items);
                coloredIcon = new L.Icon({ //green
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });
            }else if(recipient.volunteer_id != null && recipient.volunteer_id != volunteerID){
                console.log('in');
                coloredIcon = new L.Icon({ //black
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-black.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });
                recipientDeleteBtn.innerText = 'Taken by: '+recipient.volunteer_phone;
                recipientDeleteBtn.style.backgroundColor = 'black';
                recipientDeleteBtn.style.cursor = 'not-allowed';
            }
            marker.remove();
            let newMarker = L.marker([lat,lng], {icon: coloredIcon}).addTo(map);
            recipientSelectBtn.style.display = 'none';
            recipientDeleteBtn.style.display = 'block';
            let isMouseOverNew = false;
            // Add a mouseover event listener to the newMarker
            newMarker.on('mouseover', function (e) {
                isMouseOverNew = true;
                showRecipientDiv(e, recipientDiv);
            }); 
        }

            if (cluster.recipients.length > 1) {
                // Cluster marker
                marker = L.marker([lat, lng], {
                    icon: L.divIcon({
                        html: `<div class="cluster-marker">${cluster.recipients.length}</div>`,
                        className: 'cluster',
                        iconSize: [30, 30]
                    })
                });
                marker.addTo(map);
            }else{
                marker = L.marker([lat, lng]);
                marker.addTo(map);
            }


                const volunteerID = document.getElementById('volunteerID').value;
                if(recipient.volunteer_id == volunteerID || (recipient.volunteer_id != null && recipient.volunteer_id != volunteerID)) selectRecipient();
                



                
                marker.on('mouseover', (e) => {
                    isMouseOver = true;
                    showRecipientDiv(e, recipientDiv);
                });

                marker.on('mouseout', function () {
                    isMouseOver = false;
                    setTimeout(function () {
                        if (!isMouseOver) {
                            recipientDiv.style.display = 'none';
                        }
                    }, 300);
                });
                // Add a mouseover event listener to the div
                recipientDiv.addEventListener('mouseover', function () {
                    isMouseOver = true;
                    document.querySelector('body').appendChild(style); //add the style that sets the scroll bar to shown always 
                });
                // Add a mouseout event listener to the div
                recipientDiv.addEventListener('mouseout', function () {
                    isMouseOver = false;
                    setTimeout(function () {
                        if (!isMouseOver) {
                            recipientDiv.style.display = 'none';
                            // document.querySelector('body').removeChild(style);
                        }
                    }, 200);
                });

                // let newMarker;
            // }
        });

            spawnedRecipientMarkers.push(marker);
        });
    }
    
    function showRecipientDiv(e, recipientDiv) {
        // Append the div to the page
        document.querySelector('body').appendChild(recipientDiv);
        recipientDiv.style.display = 'grid';
        recipientDiv.style.top = e.originalEvent.clientY + window.scrollY - recipientDiv.clientHeight+ "px";
        recipientDiv.style.left = e.originalEvent.clientX -recipientDiv.clientWidth/2 + "px";
    }


    // Update clusters initially and on map movements
    updateClusters();
    map.on('moveend', updateClusters);

    // Handle map center and zoom level changes
    map.on('moveend', function() {
        const center = map.getCenter();
        localStorage.setItem('mapCenterLat', center.lat);
        localStorage.setItem('mapCenterLng', center.lng);
    });

    map.on('zoomend', function() {
        const zoom = map.getZoom();
        localStorage.setItem('mapZoom', zoom);
    });
    
    let zoomLevel;
    if(minDistance < 0.5)      zoomLevel = 15;
    else if(minDistance < 1)   zoomLevel = 14;
    else if(minDistance < 5)   zoomLevel = 13;
    else if(minDistance < 10)  zoomLevel = 12;
    else if(minDistance < 20)  zoomLevel = 11;
    else if(minDistance < 50)  zoomLevel = 10;
    else if(minDistance < 100) zoomLevel = 9;
    else if(minDistance < 200) zoomLevel = 8.5;
    else if(minDistance < 500) zoomLevel = 8;
    else zoomLevel = 7;

    if (!zoom) map.setZoom(zoomLevel); // Initialize with default zoom level










    const summaryHeader = document.getElementById('selected-recipients-header');
    const collected = document.getElementById('collected').value;
    console.log(collected);

// ---------------------------- Collect Locations ----------------------------
if(collected==0 && collectLocation){
    summaryHeader.innerHTML = `<h2 class="center-text">Collect Locations:</h2>`;
        selectedRecipients.sort((a, b) => a.distance - b.distance);

        //recreate it as a table
        const recipientsCollectTable = document.createElement('table');
        recipientsCollectTable.innerHTML = `
        <tr>
            <th>Starting Time</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Maps</th>
        </tr>
        `;
        collectLocation.forEach(collectLocation => {
            const recipientRow = document.createElement('tr');
            recipientRow.innerHTML = `
            <td>${collectLocation.pickup_time}</td>
            <td><a href="tel:${collectLocation.phone}">${collectLocation.phone}</a></td>
            <td>${collectLocation.address}</td>
            <td class="center-text">
            <a href="https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(collectLocation.address)}" target="_blank">Google</a>
            <hr>
            <a href="http://maps.apple.com/?daddr=${encodeURIComponent(collectLocation.address)}" target="_blank">Apple</a>
            </td>  

            `;
            recipientsCollectTable.appendChild(recipientRow);
        });

        summaryHeader.appendChild(recipientsCollectTable);

        //confirm pickup button
        const pickupHeader = document.createElement('h4');
        pickupHeader.innerText = 'If you arrived at the pickup location and picked up the '+totalItems+' boxes, please click the button below to confirm the pickup.';
        pickupHeader.style.textAlign = 'center';
        pickupHeader.style.margin = '0 0 3px 0';
        summaryHeader.appendChild(pickupHeader);
        
        const confirmPickupBtn = document.createElement('button');
        confirmPickupBtn.classList.add('general-button');
        confirmPickupBtn.style.backgroundColor = 'blue';
        confirmPickupBtn.style.width = '60%';
        confirmPickupBtn.style.marginLeft = '20%';
        confirmPickupBtn.style.marginBottom = '4px';
        
        confirmPickupBtn.innerText = 'Confirm Pickup';
        confirmPickupBtn.addEventListener('click', ()=>{
            if(confirm('Are you sure you want to confirm the pickup of the selected recipients?'))
            window.location.href = 'php/select_recipient.php?for=pickup';
        });
        summaryHeader.appendChild(confirmPickupBtn);
        summaryHeader.appendChild(document.createElement('hr'));
    }


// ---------------------------- Recipients Table ----------------------------

    
    const recipientsTableDiv = document.getElementById('recipients-table');
    
    const totalItemsDiv = document.createElement('div');
    totalItemsDiv.classList.add('center-text');
    totalItemsDiv.innerHTML = `<h2 style="color:red; margin: 0 !important; margin-top: 5px !important;">Total Required Boxes: ${totalItems}</h2> <br> <h4 style="margin:0">Please confirm the delivery after dropping off each box to it's recipient.</h4>    `;
    recipientsTableDiv.appendChild(totalItemsDiv);

    const recipientsTable = document.createElement('table');

    if(confirmedRecipients.length > 0){
        recipientsTable.innerHTML = `
        <tr>
            <th>Recipient</th>
            <th>Phone</th>
            <th>Items</th>
            <th>Address</th>
            <th>Maps</th>
            <th>Actions</th>
        </tr>
        `;
        selectedRecipients.forEach(recipient => {
            const recipientRow = document.createElement('tr');
            recipientRow.innerHTML = `
            <td>${recipient.name}</td>
            <td><a href="tel:${recipient.phone}">${recipient.phone}</a></td>
            <td>${recipient.items}</td>
            <td>
            ${recipient.apt_num == null ? '' : 'Apt:'+recipient.apt_num+'<hr>'}
            ${recipient.address}, ${recipient.city}
            <br>
            ${recipient.hotel_info == null ? '' : 'Hotel:'+recipient.hotel_info}
            <td class="center-text">
            <a href="https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(recipient.address + ', ' + recipient.city)}" target="_blank">Google</a>
            <hr>
            <a href="http://maps.apple.com/?daddr=${encodeURIComponent(recipient.address + ', ' + recipient.city)}" target="_blank">Apple</a>
            </td>
            <td>
            ${recipient.status != 'Confirmed' ? `<button style="background-color:green;" class="general-button" onclick="window.location.href='php/select_recipient.php?recipient_id=${recipient.id}&for=confirm'">Confirm</button>` : ''}       
            ${recipient.status == 'Confirmed' ? `
            <button style="font-size:9px; background-color:red; color:white" class="general-button" onclick="if(confirm('Are you sure you want to deselect recipient ${recipient.name}?')) window.location.href='php/select_recipient.php?recipient_id=${recipient.id}&for=deselect'">Deselect</button>
            ` : ''}
            </td>
            `;
            // <button style="font-size:9px; background-color:blue; color:white" class="general-button" onclick="if(confirm('Are you sure you want to confirm this recipient as Delivered?')) window.location.href='php/select_recipient.php?recipient_id=${recipient.id}&for=completed'">Confirm Delivery</button>
            recipientsTable.appendChild(recipientRow);
        });
    }
    else{
        recipientsTable.innerHTML = `
        <tr>
            <th>Items</th>
            <th>Address</th>
            <th>Maps</th>
        </tr>
        `;
        selectedRecipients.forEach(recipient => {
            const recipientRow = document.createElement('tr');
            recipientRow.innerHTML = `
            <td>${recipient.items}</td>
            <td>${recipient.address} , ${recipient.city}</td>
            <td class="center-text">
            <a href="https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(recipient.address + ', ' + recipient.city)}" target="_blank">Google</a>
            <hr>
            <a href="http://maps.apple.com/?daddr=${encodeURIComponent(recipient.address + ', ' + recipient.city)}" target="_blank">Apple</a>
            </td>
            `;
            recipientsTable.appendChild(recipientRow);
        });
    }
    recipientsTableDiv.appendChild(recipientsTable);
    
    const condirmBtn = document.createElement('button');
    condirmBtn.classList.add('general-button');
    if(selectedRecipients.length == 0){
        const paragraph = document.createElement('h1');
        paragraph.style.color = 'red';
        paragraph.innerText = 'Please Select recipients from the map';
        recipientsTableDiv.appendChild(paragraph);
    }
    else{
        if(selectedRecipients.length == confirmedRecipients.length){
            const generalConfrimBtn = document.getElementById('general-confirm-button');
            generalConfrimBtn.innerHTML = `All ${selectedRecipients.length} Selected Recipients Are Confirmed`;
            generalConfrimBtn.style.backgroundColor = 'darkgray';
            generalConfrimBtn.style.color = 'black';
            generalConfrimBtn.style.cursor = 'not-allowed';
            generalConfrimBtn.style.display = 'block';

            showSummary();
            condirmBtn.style.backgroundColor = 'green';
            condirmBtn.style.color = 'white';
            condirmBtn.innerText = 'Open Route On Google Maps';
            // arrange the recipients based on their distance from the user
            condirmBtn.addEventListener('click', function(){
                if (confirm('You will be redirected to Google Maps to view the route of the selected recipients')) {
                    // Adjust the number of waypoints based on the device
                    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                    const maxWaypoints = isMobile ? 10 : 15; // Fewer waypoints for mobile devices
                    const limitedRecipients = confirmedRecipients.slice(0, maxWaypoints);
            
                    // Encode recipient locations
                    let recipientLocations = limitedRecipients.map(confirmedRecipient =>
                        encodeURIComponent(`${confirmedRecipient.address}, ${confirmedRecipient.city}`)
                    );
            
                    // Join waypoints with "|"
                    let recipientLocationsString = recipientLocations.join('|');
            
                    // Generate Google Maps link with optimization
                    let googleMapsLink = `https://www.google.com/maps/dir/?api=1&waypoints=${recipientLocationsString}&optimize=true`;
            
                    // Open the link in a new tab
                    window.open(googleMapsLink);
                }
            });
            const deleteAllBtn = document.createElement('button');
            deleteAllBtn.classList.add('general-button');
            deleteAllBtn.style.backgroundColor = 'red';
            deleteAllBtn.style.color = 'white';
            deleteAllBtn.style.marginBottom = '5px';
            deleteAllBtn.innerText = 'Deselect All';
            deleteAllBtn.addEventListener('click', function(){
                if(confirm('Are you sure you want to delete all recipients?')){
                    window.location.href = 'php/select_recipient.php?for=deleteAll&recipients='+selectedRecipients.map(recipient => recipient.id).join(',');
                }
            });
            recipientsTableDiv.appendChild(deleteAllBtn);
        }else{
            // setTimeout(() => {
            //     if(selectedRecipients.length >0){
            //         alert(`Please Don't forget to confirm the recipients after selecting all of them from the bottom of the page`);
            //     }
            // }, 1000);
            const volunteerName = document.getElementById('volunteerName').value;

            const generalConfrimBtn = document.getElementById('general-confirm-button');
            generalConfrimBtn.style.display = 'block';
            generalConfrimBtn.innerHTML = `Confirm All ${selectedRecipients.length} Selected Recipients to ${volunteerName}`;
            generalConfrimBtn.addEventListener('click', function(){
                if(confirm(`Are you sure you want to confirm these recipients to ${volunteerName}?`)){
                    window.location.href = 'php/select_recipient.php?for=confirmAll&recipients='+selectedRecipients.map(recipient => recipient.id).join(',');
                }
            });

            condirmBtn.innerText = 'Confirm All Recipients';
            condirmBtn.addEventListener('click', function(){
                if(confirm(`Are you sure you want to confirm these recipients to ${volunteerName}?`)){
                    window.location.href = 'php/select_recipient.php?for=confirmAll&recipients='+selectedRecipients.map(recipient => recipient.id).join(',');
                }
            });
        }
        recipientsTableDiv.appendChild(condirmBtn);
    }




}









// locattion of the recipient


// let userLocation






document.addEventListener('DOMContentLoaded', ()=>{
    //fetch recipients
    console.log('fetching');
    fetch('php/admin_fetch_recipients.php')
        .then(response => response.json())
        .then(data => {
            console.log(data.recipients);
            initMap(data.recipients, data.collectLocation);
        });

    //locate me
    const locateBtn = document.getElementById('locate-button-div');
    locateBtn.addEventListener('click',locateMe);

    //location input
    var locationInput = document.querySelector('.search-bar');    
    locationInput.addEventListener('change', function () {
        // Get the user's input
        var userInput = locationInput.value;
        // Send a request to the OpenCage geocoding API
        const apiKey = 'OPEN_CAGE_API_KEY'; // Replace with your OpenCage API key
        fetch(`https://api.opencagedata.com/geocode/v1/json?q=${userInput}&key=${apiKey}`)
            .then(response => response.json())
            .then(data => {
                if (data.results.length > 0) {
                    // Use the latitude and longitude of the first result
                    var lat = data.results[0].geometry.lat;
                    var lng = data.results[0].geometry.lng;        
                    sessionStorage.setItem('userLocation', JSON.stringify({ type: 'coords', value: { lat: lat, lng: lng } }));
                    //delete local storage
                    localStorage.removeItem('mapCenterLat');
                    localStorage.removeItem('mapCenterLng');
                    localStorage.removeItem('mapZoom');
                    //refresh the page
                    location.reload();
                }else{
                    alert('No results found');
                }
            });
    });
});

function locateMe() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, locateError);
    }
    async function showPosition(position) {
        const latitude = position.coords.latitude;
        const longitude = position.coords.longitude;
        sessionStorage.setItem('userLocation', JSON.stringify({ type: 'coords', value: { lat: latitude, lng: longitude } }));
        //delete local storage
        localStorage.removeItem('mapCenterLat');
        localStorage.removeItem('mapCenterLng');
        localStorage.removeItem('mapZoom');

        location.reload();
        

        //referse geocoding
        // const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`);
        // const data = await response.json();
        // const address = data.display_name;

    }

    function locateError(error) {
        const locateErrorMsg = document.getElementById('locate-me-error');
        locateErrorMsg.style.display = 'block';
        switch (error.code) {
            case error.PERMISSION_DENIED:
                locateErrorMsg.style.display = 'block';
                locateErrorMsg.innerHTML = "Denied access for your location."
                break;
            case error.POSITION_UNAVAILABLE:
                // Handle the error
                break;
        }
    }
}


//bottom button
const summary = document.getElementById('summary');
const arrowUp = document.querySelector('.fa-arrow-up');
const arrowDown = document.querySelector('.fa-arrow-down');

const style = document.createElement('style');
style.innerHTML = `
::-webkit-scrollbar {
    -webkit-appearance: none;
    width: 7px;
    }
    ::-webkit-scrollbar-thumb {
    border-radius: 4px;
    background-color: rgba(0, 0, 0, .5);
    -webkit-box-shadow: 0 0 1px rgba(255, 255, 255, .5);
    }
    `;



arrowUp.onclick = showSummary;
arrowDown.onclick = hideSummary;


function hideSummary(){
    summary.style.height = '50px';
    summary.style.transition = 'all 0.5s';
    arrowDown.style.display = 'none';
    arrowUp.style.display = 'block';
    document.querySelector('body').removeChild(style);
}
function showSummary(){
    summary.style.height = '70vh';
    summary.style.transition = 'all 0.5s';
    arrowUp.style.display = 'none';
    arrowDown.style.display = 'block';
    document.querySelector('body').appendChild(style);
}

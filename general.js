//side bar
const sMenuButton = document.getElementById("show-menu-button");
const hMenuButton = document.getElementById("hide-menu-button");

const navLinksList = document.getElementById("nav-list-div");
sMenuButton.addEventListener('click', ()=>{
    navLinksList.style.right="0";
    // navLinksList.style.display="flex";
});
hMenuButton.addEventListener('click', ()=>{
    navLinksList.style.right="-250px";
    // navLinksList.style.display="none";
});

document.querySelector('.fa-globe').addEventListener('click', function(){
    const popup = document.getElementById('languagePopup');
    popup.style.display = "block";
    popup.addEventListener('click', function(){
        window.location.reload();
    });
});

langWarning = {
     English: "One or more fields are filled in, changing the language will clear all fields. Are you sure you want to continue?",
    Arabic: "واحد أو أكثر من الحقول مليئة، تغيير اللغة سيؤدي إلى مسح جميع الحقول. هل أنت متأكد أنك تريد المتابعة؟",
    Farsi: "یک یا چند فیلد پر شده‌اند، تغییر زبان باعث پاک شدن همه فیلدها خواهد شد. آیا مطمئن هستید که می‌خواهید ادامه دهید؟",
    Spanish: "Uno o más campos están llenos, cambiar el idioma borrará todos los campos. ¿Estás seguro de que quieres continuar?",
    Urdu: "ایک یا زیادہ فیلڈز بھری ہوئی ہیں، زبان تبدیل کرنے سے تمام فیلڈز صاف ہو جائیں گی۔ کیا آپ واقعی جاری رکھنا چاہتے ہیں؟",
    Myanmar: "တစ်ခု သို့မဟုတ် တစ်ခုထက်ပိုသော အကွက်များကို ဖြည့်ထားပါသည်။ ဘာသာစကားပြောင်းလဲခြင်းသည် အကွက်အားလုံးကို ရှင်းလင်းသွားစေမည်။ ဆက်လုပ်လိုသည်မှာ သေချာပါသလား။",
    Pashto: "یو یا څو ساحې ډکې دي، د ژبې بدلول به ټولې ساحې پاکې کړي. ایا تاسو ډاډه یاست چې غواړئ ادامه ورکړئ؟"
}

// Language selection

function changeLanguage() {
    //warn the user if he is about to change the language and there are fields filled in
    const savedLanguage = localStorage.getItem('preferredLanguage');
    const inputs = document.querySelectorAll('input');
    let refresh = false;
    inputs.forEach(input => {
        if(input.value != '' ){
            refresh = true;
        }
    });
    if(refresh & !confirm(langWarning[savedLanguage])){
        return;
    }
    
    const selectedLanguage = document.getElementById('language').value;
    const elements = document.querySelectorAll('[English]');
    localStorage.setItem('preferredLanguage', selectedLanguage);
    

    elements.forEach(element => {
        switch (selectedLanguage) {
            case 'Arabic':
                element.textContent = element.getAttribute('Arabic');
                document.querySelector('body').style.direction = 'rtl';
                break;
            case 'Farsi':
                element.textContent = element.getAttribute('Farsi');
                document.querySelector('body').style.direction = 'rtl';
                break;
            case 'Spanish':
                element.textContent = element.getAttribute('Spanish');
                document.querySelector('body').style.direction = 'ltr';
                break;
            case 'Urdu':
                element.textContent = element.getAttribute('Urdu');
                document.querySelector('body').style.direction = 'rtl';
                break;
            case 'Myanmar':
                element.textContent = element.getAttribute('Myanmar');
                document.querySelector('body').style.direction = 'rtl';
                break;
            case 'Pashto': 
                element.textContent = element.getAttribute('Pashto');
                document.querySelector('body').style.direction = 'rtl';
                break;
            default:
                element.textContent = element.getAttribute('English');
                document.querySelector('body').style.direction = 'ltr';

        }
        window.location.reload();
    });
}

function setLanguage(language) {

    const elements = document.querySelectorAll('[Arabic], [Pashto], [Myanmar], [Urdu], [Spanish], [Farsi]');
    elements.forEach(el => {
        if (language === 'Arabic') {
            el.textContent = el.getAttribute('Arabic');
            document.querySelector('body').style.direction = 'rtl';
        } else if (language === 'Farsi') {
            el.textContent = el.getAttribute('Farsi');
            document.querySelector('body').style.direction = 'rtl';
        }else if (language === 'Spanish') {
            el.textContent = el.getAttribute('Spanish');
        } else if (language === 'Urdu') {
            el.textContent = el.getAttribute('Urdu');
        } else if (language === 'Myanmar') {
            el.textContent = el.getAttribute('Myanmar');
        } else if (language === 'Pashto') {
            el.textContent = el.getAttribute('Pashto');
        }else {
            
        }
    });

    // Store preference in local storage
    localStorage.setItem('preferredLanguage', language);

    // Hide popup after selection
    document.getElementById('languagePopup').style.display = 'none';
}

// Show popup only if no language is set
window.onload = function() {
    const preferredLanguage = localStorage.getItem('preferredLanguage');
    const selectedLanguage = document.getElementById('language');
    if(selectedLanguage) selectedLanguage.value = preferredLanguage;

    if (!preferredLanguage) {
        document.getElementById('languagePopup').style.display = 'block';
    } else {
        setLanguage(preferredLanguage);
    }
};

const cityInput = document.getElementById('city');
const addressInput = document.getElementById('address');




//address to coordinates

if (addressInput && cityInput) {
    addressInput.addEventListener('change',()=>{
        const city = cityInput.value;
        const address = addressInput.value;
        userInput = address + ', ' + city;
        convertAddressToCoordinates(userInput)
    });
    cityInput.addEventListener('change',()=> {
        const city = cityInput.value;
        const address = addressInput.value;
        userInput = address + ', ' + city;
        convertAddressToCoordinates(userInput)
    })
}


function convertAddressToCoordinates(userInput) {
    const apiKey = 'OPEN_CAGE_API_KEY'; // Replace with your OpenCage API key
    fetch(`https://api.opencagedata.com/geocode/v1/json?q=${userInput}&key=${apiKey}`)
        .then(response => response.json())
        .then(data => {
            if (data.results.length > 0) {                
                // Use the latitude and longitude of the first result
                var lat = data.results[0].geometry.lat;
                var lng = data.results[0].geometry.lng;        
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
                return {lat, lng};
            }else{
                return 'failed';
            }
        });
}
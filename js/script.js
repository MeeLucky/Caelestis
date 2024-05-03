function getTurnover() {
    let category = $("#category-selector").val();
    let storage = $("#storage-selector").val();
    
    $.ajax({
        url: '../php/getTurnover.php',         /* Куда пойдет запрос */
        method: 'get',             /* Метод передачи (post или get) */
        data: "storage="+storage+"&category="+category,
        success: function(response){   /* функция которая будет выполнена после успешного запроса.  */
            // console.log(response);            /* В переменной data содержится ответ от index.php. */
            $(".result").html(response);
        }
    });
}

//берёт файл из инпута и загружает на сервер для обработки
function upload() {
    //получаем файл из инпута
    let maxFileSize = 5242880;
    let input = document.getElementById("file-select");
    let file = input.files[0];
    if (file == undefined) {
        // alert("файл не выбран");
        // return;
    }

    if (window.FormData === undefined) {
		alert('В вашем браузере FormData не поддерживается')
	} else {
		var formData = new FormData();
		formData.append('file', $("#file-select")[0].files[0]);
 
		$.ajax({
			type: "POST",
			url: '../php/upload.php',
			cache: false,
			contentType: false,
			processData: false,
			data: formData,
			dataType : 'json',
			success: function(msg){
				if (msg.error == '') {
					alert("Файл '"+msg.success+"' успешно загружен.");
                    parseXLS(msg.success);
				} else {
					alert(msg.error);
				}
			}
		});
	}
}

function parseXLS(fileName) {
    $.ajax({
        type: "POST",
        url: "../php/parseXLS.php",
        data: "fileName="+fileName,
        success: function(response){
            console.log(response);     
        }
    });
}

// /*
// Функция посылки запроса к файлу на сервере
// r_method  - тип запроса: GET или POST
// r_path    - путь к файлу
// r_args    - аргументы вида a=1&b=2&c=3...
// r_handler - функция-обработчик ответа от сервера
// */
// function SendRequest(r_method, r_path, r_args, r_handler)
// {
//     //Создаём запрос
//     var Request = CreateRequest();
    
//     //Проверяем существование запроса еще раз
//     if (!Request)
//     {
//         return;
//     }
    
//     //Назначаем пользовательский обработчик
//     Request.onreadystatechange = function()
//     {
//         //Если обмен данными завершен
//         if (Request.readyState == 4)
//         {
//             //Передаем управление обработчику пользователя
//             r_handler(Request);
//         }
//     }
    
//     //Проверяем, если требуется сделать GET-запрос
//     if (r_method.toLowerCase() == "get" && r_args.length > 0)
//     r_path += "?" + r_args;
    
//     //Инициализируем соединение
//     Request.open(r_method, r_path, true);
    
//     if (r_method.toLowerCase() == "post")
//     {
//         //Если это POST-запрос
        
//         //Устанавливаем заголовок
//         Request.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=utf-8");
//         //Посылаем запрос
//         Request.send(r_args);
//     }
//     else
//     {
//         //Если это GET-запрос
        
//         //Посылаем нуль-запрос
//         Request.send(null);
//     }
// } 


// function CreateRequest()
// {
//     var Request = false;

//     if (window.XMLHttpRequest)
//     {
//         //Gecko-совместимые браузеры, Safari, Konqueror
//         Request = new XMLHttpRequest();
//     }
//     else if (window.ActiveXObject)
//     {
//         //Internet explorer
//         try
//         {
//              Request = new ActiveXObject("Microsoft.XMLHTTP");
//         }    
//         catch (CatchException)
//         {
//              Request = new ActiveXObject("Msxml2.XMLHTTP");
//         }
//     }
 
//     if (!Request)
//     {
//         alert("Невозможно создать XMLHttpRequest");
//     }
    
//     return Request;
// } 
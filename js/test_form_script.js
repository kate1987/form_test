document.addEventListener("DOMContentLoaded", function() { 
    var validations = {
        required: function(value){
          return value !== '';
        },
        email: function(value){
          return value.match(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);
        }
      }

    function validate() {
        let testForm = document.querySelector('.test-form__form'),
            inputsArr = testForm.querySelectorAll('input'),
            errorMessage = document.querySelector(".test-form__msg.error"),
            successMessage = document.querySelector(".test-form__msg.success");
        
        testForm.addEventListener('submit', function(e){
          var i = 0;
          while (i < inputsArr.length) {
            var attr = inputsArr[i].getAttribute('data-validation'),
                rules = attr ? attr.split(' ') : '',
                parent = inputsArr[i].closest(".test-form__field"),
                j = 0;
            while (j < rules.length) {
              if(!validations[rules[j]](inputsArr[i].value)) {
                e.preventDefault();
                errorMessage.className = "test-form__msg error";
                errorMessage.innerHTML = "Invalid rule '" + rules[j] + "' for input '" + inputsArr[i].name + "'";
                parent.className = "test-form__field error";
                return false;
              }
              errorMessage.className = "test-form__field error hidden";
              successMessage.className = "test-form__msg success hidden";
              parent.className = "test-form__field";
              j++;
            }
            i++;
          }
          e.preventDefault();

          const formData = new FormData(testForm);
          const userAgent = window.navigator.userAgent;
          formData.set('action', 'testform_action');
          formData.set('client_browser', userAgent);

          fetch(testform_ajax.url, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
          })
          .then((resp) => resp.json())
          .then(function(data) {
            //console.log(data);
            if(data.success){
                successMessage.className = "test-form__msg success";
            } else {
                errorMessage.className = "test-form__msg error";
                errorMessage.innerHTML = data.msg;
            }
          })
          .catch(function(error) {
            //console.log(JSON.stringify(error));
          });
        
        }, false)
      }
      validate();
      
});
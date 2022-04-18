document.onreadystatechange = () => {
    if (document.readyState === 'complete') {
        var submit = document.querySelector("#id_submit");
        if (submit) {
        submit.onclick = function(event) {
            var checkedBoxes = document.querySelectorAll('input[type=checkbox][name=selecteduser]:checked');
            var val = [];
            for (let i = 0; i < checkedBoxes.length; i++) {
                val.push(checkedBoxes[i].value);
            }
            var users = document.querySelector('.selecteduser');
            users.value = val;
            // eslint-disable-next-line no-alert
            if (!confirm('Grant an extension to all selected submissions?')) {
                if (typeof event.cancelable !== 'boolean' || event.cancelable) {
                    event.preventDefault();
                  }
            }
          };
        }
    }
  };
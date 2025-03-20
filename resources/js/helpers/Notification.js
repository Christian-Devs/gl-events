class Notification{

    success(){
        new Noty({
            type: 'success',
            text: 'Operation Successful!',
            layout: 'topRight',
            timeout: 1000
        }).show();
    }

    alert(){
        new Noty({
            type: 'alert',
            text: 'Are you sure?',
            layout: 'topRight',
            timeout: 1000
        }).show();
    }

    error(){
        new Noty({
            type: 'error',
            text: 'Operation Failed!',
            layout: 'topRight',
            timeout: 1000
        }).show();
    }

    warning(){
        new Noty({
            type: 'warning',
            text: 'Something went wrong!',
            layout: 'topRight',
            timeout: 1000
        }).show();
    }
    
    image_validation(){
        new Noty({
            type: 'error',
            text: 'Uplaod image with size less than 1MB!',
            layout: 'topRight',
            timeout: 1000
        }).show();
    }
}

export default Notification = new Notification();
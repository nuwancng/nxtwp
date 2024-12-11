<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>NEXT-WP</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <style>
    #installForm {
      transition: opacity 0.5s ease;
    }
    #installForm.hidden {
      display: none; /* Collapse the space completely */
    }
    .loader {
      display: inline-block;
      width: 1em;
      height: 1em;
      border: 2px solid transparent;
      border-top-color: #007bff;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }
    @keyframes spin {
      from {
        transform: rotate(0deg);
      }
      to {
        transform: rotate(360deg);
      }
    }
  </style>
</head>
<body>
<div class="container">
  <div class="row">
    <div class="col-md-12" style="max-width:500px;margin:auto;padding-top:50px">
      <center><h1>NEXTWP</h1></center>
      <form id="installForm">
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Email address</label>
          <input type="email" class="form-control" id="email" aria-describedby="emailHelp" name="email" required>
          <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
        </div>
        <div class="mb-3">
          <label for="exampleInputyourname" class="form-label">Your Name</label>
          <input type="text" class="form-control" id="customer_name" name="customer_name" required>
        </div>
        <button type="submit" class="btn btn-primary">Create My WP site</button>
      </form>
      <div id="response" class="mt-3 text-center"></div>
    </div>
  </div>
</div>
<script>
  document.getElementById('installForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent form submission

    const customer_name = document.getElementById('customer_name').value;
    const email = document.getElementById('email').value;
    const responseElement = document.getElementById('response');

    // Show loader animation
    responseElement.innerHTML = '<div class="loader"></div><p>Generating...</p>';

    // Create an AJAX request
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'generate.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
      if (xhr.status === 200) {
        try {
          // Parse the JSON response
          const data = JSON.parse(xhr.responseText);
          if (data.status === 'success') {
            responseElement.innerHTML = `
              <div class="alert alert-success">
                <h4>${data.message}</h4>
                <p><strong>Site URL:</strong> <a href="${data.url}" target="_blank">${data.url}</a></p>
                <p><strong>Admin Username:</strong> ${data.username}</p>
                <p><strong>Admin Password:</strong> ${data.password}</p>
                <a href="${data.url}" class="btn btn-primary" target="_blank">Visit Your Site</a>
                <a href="${data.url}/wp-admin" class="btn btn-secondary" target="_blank">Go to Admin Dashboard</a>
              </div>`;
          } else {
            responseElement.innerHTML = `<div class="alert alert-danger"><p>Error: ${data.message || 'Unknown error occurred.'}</p></div>`;
          }
        } catch (error) {
          responseElement.innerHTML = `<div class="alert alert-danger"><p>Error parsing response: ${xhr.responseText}</p></div>`;
        }
      } else {
        responseElement.innerHTML = `<div class="alert alert-danger"><p>Error: ${xhr.status}</p></div>`;
      }
    };

    // Hide the form completely
    document.getElementById('installForm').classList.add('hidden');

    // Send the data
    xhr.send('customer_name=' + encodeURIComponent(customer_name) + '&email=' + encodeURIComponent(email));
  });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN6jIeHz" crossorigin="anonymous"></script>
</body>
</html>

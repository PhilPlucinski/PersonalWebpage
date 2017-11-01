<!DOCTYPE html>
<html>
<head>
  <title>R&amp;M Scrape Test</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <style>
  .morty {
    max-height:100px;
  }
  input {
    display: block;
  }

  .tooltiptext:hover {
     visibility: visible;
     opacity: 1;
  }

  .error {
    border: 1px solid red;
  }
  </style>
</head>
<body>
  <h1>Judson 4th Floor - Morty Mart</h1>
  <h4>The <strong>least recently chosen</strong> Morty will be your door deck. Please only claim one Morty at a time.</h4>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>#</th>
        <th>Name</th>
        <th>Image</th>
        <th>Claimed By</th>
        <th>Claimed On</th>
        <th>Claim/Release</th>
      </tr>
    </thead>
    <tbody id="tbody">
    </tbody>
  </table>
  <div id="dialog-form" title="Claim Your Morty!">
    <form id="form">
      <fieldset>
        <label for="name">Name</label>
        <input type="text" name="name" id="name" placeholder="Eshan Kaul">
        <label for="number">Suite Number</label>
        <input type="text" name="number" id="number" placeholder="4406" maxlength="4">
        <label for="releasePhrase"><a href="#" data-toggle="tooltip" title="If you want to unclaim your Morty, you'll need to type in this phrase.">Release Phrase</a></label>
        <input type="text" name="releasePhrase" id="releasePhrase" placeholder="Sea Cucumber">
        <input style="display: none;" type="text" name="mortyid" id="mortyid">

        <input type="submit" tabindex="-1" style="position:absolute; top:-500px">
      </fieldset>
    </form>
  </div>
  <div id="dialog-form-2" title="Release Your Morty!">
    <form id="form2">
      <fieldset>
        <label for="releasePhrase2"><a href="#" data-toggle="tooltip" title="This is the phrase you set when you claimed your morty. You claimed this particular morty, right?">Release Phrase</a></label>
        <input type="text" name="releasePhrase" id="releasePhrase2" placeholder="Sea Cucumber">
        <input style="display: none;" type="text" name="mortyid" id="mortyid2">

        <input id="focus" type="submit" tabindex="-1" style="position:absolute; top:-500px">
      </fieldset>
    </form>
  </div>
  <script>
  var number = /^\d{4}$/;
  var phrase = /^[A-Za-z\s-]{2,}[a-z]$/;

  $(document).ready(function () {
    var mortys;
    $.ajax({
      url: "allMortys.php",
      success: function(result) {
        mortys = JSON.parse(result);
        displayMortys(mortys);
      },
      error: function(e) {
        alert(JSON.stringify(e, null, 2));
      }
    });
    $('[data-toggle="tooltip"]').tooltip();

    $("#name").keyup([phrase, $("#name")], validate);
    $("#number").keyup([number, $("#number")], validate);
    $("#releasePhrase").keyup([phrase, $("#releasePhrase")], validate);
    $("#releasePhrase2").keyup([phrase, $("#releasePhrase2")], validate);


  });

  function displayMortys(mortys) {
    for (var i = 0; i < mortys.length; i++) {
      $("tbody").append("<tr>");
      var row = $("tbody tr").last();

      row.append("<td><strong>" + mortys[i][0] + "</strong></td>");
      row.append("<td><strong>" + mortys[i][1] + "</strong></td>");
      row.append("<td><img class='img-responsive morty' src='" + mortys[i][2] + "'></img></td>");
      var claimedby = mortys[i][3] + " - " + mortys[i][4];
      if (mortys[i][3] == null && mortys[i][4] == null) {
        claimedby = "-";
      }
      row.append("<td><strong>" + claimedby + "</strong></td>");
      var claimedon = mortys[i][5];
      if (claimedon == null) {
        claimedon = "-";
      }
      row.append("<td><strong>" + claimedon + "</strong></td>");
      if (mortys[i][3] == null) {
        row.append("<td><button class='btn btn-primary' onclick='claim(" + mortys[i][0] + ", " + i + ")'>Claim</button></td>");
      } else {
        row.append("<td><button class='btn btn-primary' onclick='release(" + mortys[i][0] + ", " + i + ")'>Release</button></td>");
      }
    }
  }

  var mortyid;
  var rownum;

  function claim(id, num) {
    mortyid = id;
    rownum = num;
    claimDialog.dialog( "open" );
  }

  function processClaim(){
    if (!(validate({data:[phrase, $("#name")]}) && validate({data:[number, $("#number")]}) && validate({data:[phrase, $("#releasePhrase")]}))) {
      alert("One of the fields does not contain a valid value. Try again.");
      return;
    }
    //alert(mortyid + "," + rownum + $("#name").val() + $("#number").val());
    $("#mortyid").val(mortyid);
    var serializedData = $("#form").serialize();
    $.ajax({
      url: "claimMorty.php",
      type: "post",
      data: serializedData,
      success: function(result) {
        if (result == 1) {
          alert("Morty successfully claimed.");
          var name = $("#name").val();
          var number = $("#number").val();
          var combinedName = name + " - " + number;
          var timestamp = new Date().toString();
          var nameCol = "#tbody tr:nth-child(" + (rownum+1) + ") td:nth-child(4)";
          var timeCol = "#tbody tr:nth-child(" + (rownum+1) + ") td:nth-child(5)";
          var buttonCol = "#tbody tr:nth-child(" + (rownum+1) + ") td:nth-child(6)";
          $(nameCol).html("<strong>" + combinedName + "</strong>");
          $(timeCol).html("<strong>" + timestamp + "</strong>");
          var row = $(timeCol).parent();
          $(buttonCol).remove();
          row.append("<td><button class='btn btn-primary' onclick='release(" + mortyid + ", " + rownum + ")'>Release</button></td>");
          claimDialog.dialog("close");
        } else {
          alert("That morty was already claimed.");
        }
      },
      error: function(e) {
        alert(e.message);
      }
    });
  }

  var claimDialog = $("#dialog-form").dialog({
    autoOpen: false,
    height: 280,
    width: 190,
    modal: true,
    buttons: [
      {
        text: "Claim",
        click: processClaim
      },
      {
        text: "Cancel",
        click: function() {
          claimDialog.dialog("close");
        }
      }
    ],
    close: function() {
      claimDialog.find("form")[0].reset();
    }
  });

  claimDialog.find("form").on("submit", function( event ) {
    event.preventdefault();
  });

  function release(id, num) {
    mortyid = id;
    rownum = num;
    releaseDialog.dialog( "open" );
    $("#focus").focus();
  }

  function processRelease(){
    //alert(mortyid + "," + rownum + $("#name").val() + $("#number").val());
    $("#mortyid2").val(mortyid);
    var serializedData = $("#form2").serialize();
    $.ajax({
      url: "releaseMorty.php",
      type: "post",
      data: serializedData,
      success: function(result) {
        if (result == 1) {
          alert("Morty successfully released.");
          var nameCol = "#tbody tr:nth-child(" + (rownum+1) + ") td:nth-child(4)";
          var timeCol = "#tbody tr:nth-child(" + (rownum+1) + ") td:nth-child(5)";
          var buttonCol = "#tbody tr:nth-child(" + (rownum+1) + ") td:nth-child(6)";
          $(nameCol).html("<strong>-</strong>");
          $(timeCol).html("<strong>-</strong>");
          var row = $(timeCol).parent();
          $(buttonCol).remove();
          row.append("<td><button class='btn btn-primary' onclick='claim(" + mortyid + ", " + rownum + ")'>Claim</button></td>");
          releaseDialog.dialog("close");
        } else {
          alert("Incorrect release phrase or morty was already released. See RA Eshan if this error is unexpected.");
        }

      },
      error: function(e) {
        alert(e.message);
      }
    });
  }

  var releaseDialog = $("#dialog-form-2").dialog({
    autoOpen: false,
    height: 180,
    width: 200,
    modal: true,
    buttons: [
      {
        text: "Release",
        click: processRelease
      },
      {
        text: "Cancel",
        click: function() {
          releaseDialog.dialog("close");
        }
      }
    ],
    close: function() {
      releaseDialog.find("form")[0].reset();
    }
  });

  releaseDialog.find("form").on("submit", function( event ) {
    event.preventdefault();
  });

  function validate(args) {
    var reg = args.data[0];
    var o = args.data[1];
    if (!(reg.test(o.val()))) {
      o.addClass("error");
      return false;
    } else {
      o.removeClass("error");
      return true;
    }
  }
  </script>

</body>
</html>

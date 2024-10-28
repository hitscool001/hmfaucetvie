$("#coupon-check").click(() => {
  $("#coupon-result").html();
  const code = $("#coupon-code").val();
  $.post(
    site_url + "coupon/check",
    {
      code,
    },
    function (data, status) {
      const result = JSON.parse(data);
      if (result.status === "success") {
        $("#coupon-result").html(
          `<div class="alert alert-success text-center" role="alert">
                    <div class="alert-body">${result.message}</div>`
        );
      } else {
        $("#coupon-result").html(
          `<div class="alert alert-danger text-center" role="alert">
                    <div class="alert-body">${result.message}</div>`
        );
      }
    }
  );
});

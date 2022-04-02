var pdf = new PDFAnnotate("pdf-container", filename, {
    onPageUpdated(page, oldData, newData) {
      console.log(page, oldData, newData);
    },
    ready() {
      console.log("Plugin initialized successfully");
    },
    scale: 1.5,
    pageImageCompression: "MEDIUM", // FAST, MEDIUM, SLOW(Helps to control the new PDF file size)
  });
  
  function changeActiveTool(event) {
      var element = $(event.target).hasClass("tool-button")
        ? $(event.target)
        : $(event.target).parents(".tool-button").first();
      $(".tool-button.active").removeClass("active");
      $(element).addClass("active");
  }
  
  function enablePencil(event) {
      event.preventDefault();
      changeActiveTool(event);
      pdf.enablePencil();
  }


  function savePDF(event) {
    // pdf.savePdf();
    event.preventDefault();
    pdf.savePdf('sample.pdf'); // save with given file name
}

//   function enableAddText(event) {
//     event.preventDefault();
//     changeActiveTool(event);
//     pdf.enableAddText();
// }
  // function savePDF(event) {
  //   event.preventDefault();
  //   // pdf.savePdf();
  //   pdf.savePdf('sample.pdf'); // save with given file name
  // }
//   $(function () {
//       $('.color-tool').click(function () {
//           $('.color-tool.active').removeClass('active');
//           $(this).addClass('active');
//           color = $(this).get(0).style.backgroundColor;
//           pdf.setColor(color);
//       });
  
//       $('#brush-size').change(function () {
//           var width = $(this).val();
//           pdf.setBrushSize(width);
//       });
//   });
  
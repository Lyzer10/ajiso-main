(function () {
    window.ajisoPrintLetter = function (content) {
        if (!content) {
            return;
        }

        var popup = window.open('', '_blank', 'width=800,height=900');
        if (!popup) {
            return;
        }

        popup.document.open();
        popup.document.write('<html><head><title></title>');
        popup.document.write('<style>body{font-family:\"Times New Roman\",serif;font-size:12px;line-height:1.6;padding:30px;color:#111827;}');
        popup.document.write('.letter-head{text-align:center;}');
        popup.document.write('.letter-logo img{max-height:70px;width:auto;}');
        popup.document.write('.letter-contact{font-size:11px;margin-top:6px;}');
        popup.document.write('.letter-divider{border-top:2px solid #111827;margin:10px 0 14px;}');
        popup.document.write('.letter-meta{display:flex;justify-content:space-between;gap:12px;font-size:12px;margin-bottom:12px;}');
        popup.document.write('.letter-recipient{font-size:12px;margin-bottom:12px;}');
        popup.document.write('.letter-subject{text-align:center;font-weight:700;letter-spacing:0.04em;text-transform:uppercase;margin:16px 0;}');
        popup.document.write('.letter-body p{margin:0 0 10px;}');
        popup.document.write('.letter-pre{white-space:pre-wrap;font-family:\"Times New Roman\",serif;font-size:12px;line-height:1.6;margin:0;}');
        popup.document.write('.letter-form{margin-bottom:20px;}');
        popup.document.write('.letter-form{font-family:\"Times New Roman\",serif;font-size:12px;}');
        popup.document.write('.letter-ref-title{text-align:center;font-weight:700;margin:8px 0 10px;}');
        popup.document.write('.letter-section-title{font-weight:600;margin:8px 0 6px;}');
        popup.document.write('.letter-ref-table{width:100%;border-collapse:collapse;margin:8px 0;}');
        popup.document.write('.letter-ref-table th,.letter-ref-table td{border:1px solid #111827;padding:4px 6px;font-size:12px;vertical-align:top;}');
        popup.document.write('.letter-ref-table th{font-weight:600;text-align:left;}');
        popup.document.write('.letter-ref-center{text-align:center;font-weight:600;}');
        popup.document.write('.letter-ref-box{border:1px solid #111827;height:80px;margin:6px 0 8px;}');
        popup.document.write('.letter-ref-divider{text-align:center;font-size:11px;margin:8px 0;}');
        popup.document.write('.letter-ref-code{text-align:right;font-size:11px;margin-top:6px;}');
        popup.document.write('.letter-footnote{font-size:11px;margin-bottom:6px;}');
        popup.document.write('.letter-center{text-align:center;font-weight:600;margin-bottom:2px;}');
        popup.document.write('.letter-line-row{display:flex;align-items:baseline;gap:6px;margin:2px 0;flex-wrap:nowrap;}');
        popup.document.write('.letter-line-row--wrap{flex-wrap:wrap;}');
        popup.document.write('.letter-line-label{white-space:nowrap;}');
        popup.document.write('.letter-note{white-space:nowrap;font-size:11px;}');
        popup.document.write('.letter-dots{border-bottom:1px dotted #111827;height:12px;flex:1 1 auto;}');
        popup.document.write('.letter-dots--sm{width:90px;flex:0 0 90px;}');
        popup.document.write('.letter-dots--md{width:150px;flex:0 0 150px;}');
        popup.document.write('.letter-dots--lg{width:220px;flex:1 1 220px;}');
        popup.document.write('.letter-table{width:100%;border-collapse:collapse;margin-top:6px;}');
        popup.document.write('.letter-table td{padding:2px 4px;font-size:12px;}');
        popup.document.write('.letter-table-num{width:20px;white-space:nowrap;}');
        popup.document.write('.letter-table-question{width:60%;}');
        popup.document.write('.letter-table-option{width:60px;white-space:nowrap;}');
        popup.document.write('.letter-box{display:inline-block;width:12px;height:12px;border:1px solid #111827;vertical-align:middle;text-align:center;font-size:10px;line-height:10px;}');
        popup.document.write('.letter-box-label{margin:0 6px 0 4px;white-space:nowrap;}');
        popup.document.write('.letter-lines{display:flex;flex-direction:column;gap:6px;margin-top:6px;}');
        popup.document.write('.letter-subject{text-align:center;font-weight:700;letter-spacing:0;text-transform:uppercase;margin:16px 0;}');
        popup.document.write('.letter-subject-underline{text-decoration:underline;text-underline-offset:2px;}');
        popup.document.write('.letter-line{display:inline-block;width:160px;border-bottom:1px dotted #111827;padding:0 4px 2px;line-height:1.2;vertical-align:baseline;}');
        popup.document.write('.letter-line--xs{width:60px;}');
        popup.document.write('.letter-line--sm{width:100px;}');
        popup.document.write('.letter-line--md{width:140px;}');
        popup.document.write('.letter-line--lg{width:200px;}');
        popup.document.write('.letter-line--xl{width:260px;}');
        popup.document.write('.letter-line--short{width:140px;}');
        popup.document.write('.letter-line--medium{width:180px;}');
        popup.document.write('.letter-line--long{width:260px;}');
        popup.document.write('.letter-line--block{display:block;width:260px;}');
        popup.document.write('.letter-signature{margin-top:18px;}');
        popup.document.write('</style>');
        popup.document.write('</head><body>');
        popup.document.write(content);
        popup.document.write('</body></html>');
        popup.document.close();
        popup.focus();
        popup.print();
    };
})();

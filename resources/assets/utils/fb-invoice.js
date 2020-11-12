/*
    USAGE:

    let invoice = {
      'id': '3126064814172325-6555665',
      'ad_account_name': 'Redoa',
      'ad_account_id': '985649491933016',
      'time': 1604424742,
      'funding_source': 'Mastercard *3489',
      'reference_number': 'Z8KR3VAHA2',
      'amount': '20.00',
      'currency': 'EUR',
      'billing_reason': 'You\'re being billed because you\'ve reached your €20.00 EUR threshold.',
      'billing_start_time': 1604410222,
      'billing_end_time': 1604424742,
      'vat_invoice_id': 'FBADS-217-101143844',
    }
    this.generateInvoicePDF(invoice).save('test2.pdf')
*/

import { OpenSansRegular } from '@assets/fonts/OpenSans-Regular'
import { OpenSansBold } from '@assets/fonts/OpenSans-Bold'
import { FbInvoiceTemplate } from '@assets/images/invoices/FbInvoiceTemplate'
import jsPDF from 'jspdf'

export function invoiceFormatDateTime(d) {
  let months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  let s = ''
  s += d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear()
  s += ', ' + d.getHours().toString().padStart(2, '0') + ':' + d.getMinutes().toString().padStart(2, '0')
  return s
}

export function invoiceFormatAmount(amount, currency, currencyISOFlag = false) {
  currency = currency.toLowerCase()
  let currencySymbols = {
      'eur': '€',
      'usd': '$',
  }
  let s = ''
  if (currencySymbols.hasOwnProperty(currency)) {
    s += currencySymbols[currency] + amount
  } else {
    s += amount
  }
  if (currencyISOFlag) {
      s +=  ' ' + currency.toUpperCase()
  }
  return s
}

let jspdfInitialized = false
function initializeJSPDF() {
  if (!jspdfInitialized) {
    // Create font
    let callAddFont = function () {
        this.addFileToVFS('OpenSans-Regular-normal.ttf', OpenSansRegular);
        this.addFont('OpenSans-Regular-normal.ttf', 'OpenSans-Regular', 'normal');
        this.addFileToVFS('OpenSans-Bold-normal.ttf', OpenSansBold);
        this.addFont('OpenSans-Bold-normal.ttf', 'OpenSans-Bold', 'normal');
    }
    jsPDF.API.events.push(['addFonts', callAddFont])

    // Text alignment
    let splitRegex = /\r\n|\r|\n/g;
    jsPDF.API.textEx = function (text, x, y, hAlign, vAlign) {
        let fontSize = this.internal.getFontSize() / this.internal.scaleFactor;

        // As defined in jsPDF source code
        let lineHeightProportion = 1.15;

        let splittedText = null;
        let lineCount = 1;
        if (vAlign === 'middle' || vAlign === 'bottom' || hAlign === 'center' || hAlign === 'right') {
            splittedText = typeof text === 'string' ? text.split(splitRegex) : text;

            lineCount = splittedText.length || 1;
        }

        // Align the top
        y += fontSize * (2 - lineHeightProportion);

        if (vAlign === 'middle')
            y -= (lineCount / 2) * fontSize;
        else if (vAlign === 'bottom')
            y -= lineCount * fontSize;

        if (hAlign === 'center' || hAlign === 'right') {
            let alignSize = fontSize;
            if (hAlign === 'center')
                alignSize *= 0.5;

            if (lineCount > 1) {
                for (let iLine = 0; iLine < splittedText.length; iLine++) {
                    this.text(splittedText[iLine], x - this.getStringUnitWidth(splittedText[iLine]) * alignSize, y);
                    y += fontSize * lineHeightProportion;
                }
                return this;
            }
            x -= this.getStringUnitWidth(text) * alignSize;
        }

        this.text(text, x, y);
        return this;
    };

    jspdfInitialized = true
  }
}

export function generateInvoicePDF(invoice) {
  initializeJSPDF()

  // Generate PDF
  let doc = new jsPDF({
      unit: 'px',
  })
  
  let docW = doc.internal.pageSize.getWidth()
  let docH = doc.internal.pageSize.getHeight()

  doc.setFont('OpenSans-Regular')
  doc.setTextColor(30, 35, 42)

  // Add template
  let imgData = 'data:image/png;base64,' + FbInvoiceTemplate
  doc.addImage(imgData, "PNG", 0, 0, docW, docH)

  // Header
  doc.setFont('OpenSans-Regular')
  doc.setFontSize(11)
  doc.text("Invoice for " + invoice['ad_account_name'], 38, 42)
  doc.setFontSize(7)
  doc.text("Account ID: " + invoice['ad_account_id'], 38, 50)

  // Transaction date
  doc.setFont('OpenSans-Bold')
  doc.setFontSize(7)
  doc.text(invoiceFormatDateTime(new Date(invoice['time'] * 1000)), 38, 95)

  // Payment method
  doc.setFontSize(7)
  doc.setFont('OpenSans-Bold')
  doc.text(invoice['funding_source'], 38, 118)
  doc.setFont('OpenSans-Regular')
  doc.text("Reference number: " + invoice['reference_number'], 38, 126)

  // Transaction id
  doc.setFontSize(7)
  doc.setFont('OpenSans-Bold')
  doc.text(invoice['id'], 38, 148)

  // Amount
  doc.setFontSize(22)
  doc.setFont('OpenSans-Regular')
  doc.textEx(invoiceFormatAmount(invoice['amount'], invoice['currency'], true), 410, 128, 'right', 'bottom')
  doc.setFontSize(7)
  doc.textEx(invoice['billing_reason'], 410, 138, 'right', 'bottom')

  // Invoice from date to date and amount
  doc.setFontSize(7)
  doc.setFont('OpenSans-Regular')
  doc.text("From " + invoiceFormatDateTime(new Date(invoice['billing_start_time'] * 1000)) + 
    " to " + invoiceFormatDateTime(new Date(invoice['billing_end_time'] * 1000)), 38, 240)
  doc.setFont('OpenSans-Bold')
  doc.textEx(invoiceFormatAmount(invoice['amount'], invoice['currency'], false), 408, 240, 'right', 'bottom')

  // Invoice number
  doc.setFontSize(7)
  doc.setFont('OpenSans-Regular')
  doc.setTextColor(144, 148, 165)
  doc.textEx("Invoice no. " + invoice['vat_invoice_id'], 409, 580, 'right', 'bottom')

  return doc
}

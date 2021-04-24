export const invoiceTemplateXML = `\
<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="fatturapa_v1.2.1.xsl"?>
<p:FatturaElettronica xmlns:p="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" versione="FPR12">
  <FatturaElettronicaHeader>
    <DatiTrasmissione>
      <IdTrasmittente>
        <IdPaese>IT</IdPaese>
        <IdCodice>IT05051410263</IdCodice>
      </IdTrasmittente>
      <ProgressivoInvio>KNT5N</ProgressivoInvio>
      <FormatoTrasmissione>FPR12</FormatoTrasmissione>
      <CodiceDestinatario>5RUO82D</CodiceDestinatario>
    </DatiTrasmissione>
    <CedentePrestatore>
      <DatiAnagrafici>
        <IdFiscaleIVA>
          <IdPaese>IE</IdPaese>
          <IdCodice>IE9692928F</IdCodice>
        </IdFiscaleIVA>
        <Anagrafica>
          <Denominazione>Facebook Ireland Limited</Denominazione>
        </Anagrafica>
        <RegimeFiscale>RF01</RegimeFiscale>
      </DatiAnagrafici>
      <Sede>
        <Indirizzo>4 Grand Canal Square, Grand Canal Harbour</Indirizzo>
        <CAP>00000</CAP>
        <Comune>Dublin 2</Comune>
        <Nazione>IE</Nazione>
      </Sede>
    </CedentePrestatore>
    <CessionarioCommittente>
      <DatiAnagrafici>
        <IdFiscaleIVA>
          <IdPaese>IT</IdPaese>
          <IdCodice>05051410263</IdCodice>
        </IdFiscaleIVA>
        <Anagrafica>
          <Denominazione>Zaro S.r.l.s.</Denominazione>
        </Anagrafica>
      </DatiAnagrafici>
      <Sede>
        <Indirizzo>Via Francia 6</Indirizzo>
        <CAP>31033</CAP>
        <Comune>Castelfranco Veneto</Comune>
        <Nazione>IT</Nazione>
      </Sede>
    </CessionarioCommittente>
  </FatturaElettronicaHeader>
  <FatturaElettronicaBody>
    <DatiGenerali>
      <DatiGeneraliDocumento>
        <TipoDocumento>TD01</TipoDocumento>
        <Divisa>--CURRENCY--</Divisa>
        <Data>--DATE--</Data>
        <Numero>--NUMBER--</Numero>
        <ImportoTotaleDocumento>--AMOUNT--</ImportoTotaleDocumento>
        <Arrotondamento>0.00</Arrotondamento>
      </DatiGeneraliDocumento>
      <DatiRicezione>
        <IdDocumento>--NUMBER--</IdDocumento>
        <Data>--DATE--</Data>
      </DatiRicezione>
    </DatiGenerali>
    <DatiBeniServizi>
      <DettaglioLinee>
        <NumeroLinea>1</NumeroLinea>
        <Descrizione>Spese pubblicitarie</Descrizione>
        <Quantita>1.00</Quantita>
        <PrezzoUnitario>--AMOUNT--</PrezzoUnitario>
        <PrezzoTotale>--AMOUNT--</PrezzoTotale>
        <AliquotaIVA>0.00</AliquotaIVA>
        <Natura>N6</Natura>
      </DettaglioLinee>
      <DatiRiepilogo>
        <AliquotaIVA>0.00</AliquotaIVA>
        <Natura>N6</Natura>
        <Arrotondamento>0.00</Arrotondamento>
        <ImponibileImporto>--AMOUNT--</ImponibileImporto>
        <Imposta>0.00</Imposta>
      </DatiRiepilogo>
    </DatiBeniServizi>
    <DatiPagamento>
      <CondizioniPagamento>TP02</CondizioniPagamento>
      <DettaglioPagamento>
        <ModalitaPagamento>MP08</ModalitaPagamento>
        <DataScadenzaPagamento>--DATE--</DataScadenzaPagamento>
        <ImportoPagamento>--AMOUNT--</ImportoPagamento>
      </DettaglioPagamento>
    </DatiPagamento>
  </FatturaElettronicaBody>
</p:FatturaElettronica>`

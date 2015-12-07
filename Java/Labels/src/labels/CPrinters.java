/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package labels;

import java.awt.PrintJob;
import java.awt.print.PageFormat;
import javax.print.PrintService;
import javax.print.PrintServiceLookup;
import javax.print.attribute.HashPrintRequestAttributeSet;
import javax.print.attribute.standard.MediaSizeName;

/**
 *
 * @author martinm
 */
public class CPrinters {
    private PrintService[] printers;
    private PrintService currentPrinter;
    private HashPrintRequestAttributeSet attributes;
    private PageFormat format;
    
    
    private static CPrinters instance = new CPrinters();
    
    private CPrinters(){
        attributes=new HashPrintRequestAttributeSet();
        attributes.add(MediaSizeName.ISO_A4);
        printers=PrintServiceLookup.lookupPrintServices(null, attributes);
        currentPrinter=PrintServiceLookup.lookupDefaultPrintService();
        
    }
    
}

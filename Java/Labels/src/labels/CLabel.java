/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package labels;

import java.awt.Color;
import java.awt.Font;
import java.awt.Graphics;
import java.awt.Graphics2D;
import java.awt.Image;
import java.awt.geom.AffineTransform;
import java.awt.image.BufferedImage;
import java.awt.print.PageFormat;
import java.awt.print.Paper;
import java.awt.print.Printable;
import java.awt.print.PrinterException;
import java.awt.print.PrinterJob;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.ObjectInputStream;
import java.io.ObjectOutputStream;
import java.io.Serializable;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.print.attribute.HashPrintRequestAttributeSet;
import javax.print.attribute.standard.MediaSizeName;
import javax.swing.JOptionPane;

/**
 *
 * @author martinm
 */
public class CLabel implements Printable, Serializable {

    /*
     * Vlastnosti triedy
     */
    //Vlastnosti, kde su ulozene jednotlive polia triedy
    private String          mCompany;
    private String          mName;
    private String          mBuilding;
    private String          mAddress;
    private String          mCity;
    private String          mPostalCode;
    private String          mCountry;
    //Vlastnosti, ktore nastavuju spravanie sa stitku
    private int             mType; //kazdy bit cisla indikuje, ci sa prislusne pole zobrazi na stitku
    private int             mPostalCodePosition; //nastavuje poziciu stitku
    
    private boolean[][]     mIsPrinted; //urcuje, ktore stitky budu vytlacene
    /*
     * Staticke konstanty a vlajky
     */
    //Kazda z tychto vlajok reprezentuje jedno pole stitku. 
    //Pouziva sa na identifikaciu zobrazenych poli
    //Su nastavene tak, aby kazda konstanta predstavovala len jeden bit na prislusnej pozicii
    public static final int     COMPANY =   1;
    public static final int     NAME =      1<<1;
    public static final int     BUILDING =  1<<2;
    public static final int     ADDRESS =   1<<3;
    public static final int     CITY =      1<<4;
    public static final int     POSTAL =    1<<5;
    public static final int     COUNTRY =   1<<6;
    
    public static final int     POSTAL_BEFORE = 1;
    public static final int     POSTAL_AFTER =  2;
    public static final int     POSTAL_UNDER =  3;
    
    
    public CLabel(){
        mCompany="";
        mName="";
        mBuilding="";
        mAddress="";
        mCity="";
        mPostalCode="";
        mType=NAME|ADDRESS|CITY|POSTAL;
        mPostalCodePosition=POSTAL_BEFORE;
        mIsPrinted=new boolean[4][4];
        for (int i=0;i<4;i++){
            for(int j=0;j<4;j++){
                mIsPrinted[i][j]=true;
            }
        }

    }
     
    public void setPostalPosition(int position){
        if(position<1||position>3)
            return;
        mPostalCodePosition=position;
    }
    
    public void setPrintPosition(int x, int y, boolean value){
        if(x<0||x>4||y<0||y>4)
            return;
        mIsPrinted[x][y]=value;
    }
    
    public boolean getPrintPosition(int x, int y){
        return mIsPrinted[x][y];
    }
    
    public String getField(int field){
        switch(field){
            case COMPANY:
                return mCompany;
            case NAME:
                return mName;
            case BUILDING:
                return mBuilding;
            case ADDRESS:
                return mAddress;
            case CITY:
                return mCity;
            case POSTAL:
                return mPostalCode;
            case COUNTRY:
                return mCountry;
        }
        return null;
    }
    
    public boolean setField(int field, String value){
        switch(field){
            case COMPANY:
                mCompany=value;
                break;
            case NAME:
                mName=value;
                break;
            case BUILDING:
                mBuilding=value;
                break;
            case ADDRESS:
                mAddress=value;
                break;
            case CITY:
                mCity=value;
                break;
            case POSTAL:
                mPostalCode=value;
                break;
            case COUNTRY:
                mCountry=value;
                break;
            default:
                return false;
        }
        return true;
    }
    
    public void removeFields(int fields){
        mType=mType&(~fields);
    }
    
    public void addFields(int fields){
        mType=mType|fields;
    }
    
    public Image getLabel(int w, int h, int border){
        BufferedImage output=new BufferedImage(w,h,BufferedImage.TYPE_INT_ARGB);
        Graphics2D g = output.createGraphics();
        
        LabelMetrics metrics=new LabelMetrics(w,h,this);
        
        g.setColor(Color.BLACK);
        g.fillRect(0, 0, w, h);
        g.setColor(Color.WHITE);
        g.fillRect(border, border, w-2*border, h-2*border);
        g.setColor(Color.BLACK);
        
        for(int i=0;i<7;i++){
            metrics.drawString(1<<i, g);
        }
        
        
        
        return output;
    }
    @Deprecated
    public Image getLabel_old(int w, int h, int border){
        BufferedImage output=new BufferedImage(w,h,BufferedImage.TYPE_INT_ARGB);
        Graphics2D g = output.createGraphics();
        
        int numRows=numRows();
        int fontSize=(int)(0.9*(h-2*border)/(numRows*2));
        if(isSet(BUILDING)){
            if(mBuilding.length()*fontSize*0.5>0.85*w){
                numRows++;
                fontSize=(int)(0.9*(h-2*border)/(numRows*2));
            }
            
        }
        Font font=new Font("Arial",Font.BOLD|Font.ITALIC,fontSize);
        g.setFont(font);
        
        g.setColor(Color.BLACK);
        g.fillRect(0, 0, w, h);
        g.setColor(Color.WHITE);
        g.fillRect(border, border, w-2*border, h-2*border);
        g.setColor(Color.BLACK);
        
        int y=(int)((h/2)-(numRows-1)*fontSize);
        int x=(int)(0.6*y);
        if(isSet(COMPANY)){
            g.drawString(mCompany, x, y);
            y+=2*fontSize;
        }
        if(isSet(NAME)){
            g.drawString(mName, x, y);
            y+=2*fontSize;
        }
        
        if(isSet(BUILDING)){
            int separator=mBuilding.length();
            while(separator*fontSize*0.5>0.85*w){
                
                separator=mBuilding.lastIndexOf(" ", separator-1);
            }
            
            g.drawString(mBuilding.substring(0, separator), x, y);
            y+=2*fontSize;
            if(separator<mBuilding.length()){
                g.drawString(mBuilding.substring(separator+1), x, y);
                y+=2*fontSize;
            }
        }
        
        
        if(isSet(ADDRESS)){
            g.drawString(mAddress, x, y);
            y+=2*fontSize;
        }
        
        if(isSet(CITY)){
            if(isSet(POSTAL)){
                switch(mPostalCodePosition){
                    case POSTAL_BEFORE:
                        g.drawString(mPostalCode+" "+mCity, x, y);
                        break;
                    case POSTAL_AFTER:
                        g.drawString(mCity+", "+mPostalCode,x,y);
                        break;
                    case POSTAL_UNDER:
                        g.drawString(mCity, x, y);
                        y+=2*fontSize;
                        g.drawString(mPostalCode, x, y);
                        break;
                }
            }
            else{
                g.drawString(mCity, x, y);
            }
            y+=2*fontSize;
        }
        
        if(isSet(COUNTRY)){
            g.drawString(mCountry,x,y);
        }
        return output;
    }
    
    @Override
    public int print(Graphics g, PageFormat pageFormat, int pageIndex) throws PrinterException {
        Graphics2D g2=(Graphics2D)g;
        if(pageIndex>0)
            return NO_SUCH_PAGE;
        
        pageFormat.setOrientation(PageFormat.LANDSCAPE);
       
        double x=pageFormat.getImageableX();
        double y=pageFormat.getImageableY();
        double width=(pageFormat.getWidth())*3-2*x;
        double height=(pageFormat.getHeight())*3-2*y;
        Paper paper=pageFormat.getPaper();
        paper.setImageableArea(x, y, pageFormat.getImageableWidth()+8.503723656, 
                pageFormat.getImageableHeight());
        //pageFormat.setPaper(paper);
        
        AffineTransform transform= new AffineTransform();
        transform.scale(1/3.0, 1/3.0);
        g2.translate(x+8.503723656, y);
        g2.transform(transform);
       
        double labelWidth=width/4;
        double labelHeight=height/4;
        
        Image img=this.getLabel((int)(labelWidth-x*3), (int)(labelHeight-y*3), 5);
        for(int i=0;i<4;i++){
            for (int j=0;j<4;j++){
                if(mIsPrinted[i][j])
                    g.drawImage(img, (int)(labelWidth*i), 
                                (int)(labelHeight*j), null);
            }
        }
        
        
        return PAGE_EXISTS;
    }
    
    public void printLabels(){
        PrinterJob job=PrinterJob.getPrinterJob();
        HashPrintRequestAttributeSet attribute=new HashPrintRequestAttributeSet();
        attribute.add(MediaSizeName.ISO_A4);
        PageFormat format=job.getPageFormat(attribute);
        format.setOrientation(PageFormat.LANDSCAPE);
        job.setPrintable(this, format);
        if(job.printDialog()){
            try {
                job.print(attribute);
            } catch (PrinterException ex) {
                Logger.getLogger(CLabel.class.getName()).log(Level.SEVERE, null, ex);
            }
        }
    }
    
    public void printAll(){
        
    }
    
    public boolean isSet(int field){
        return (mType&field)>0;
    }
    
    private int numRows(){
        int numRows=0;
        for (int i=0;i<7;i++){
            if(isSet(1<<i)){
                if(((1<<i)==POSTAL)&&(mPostalCodePosition!=POSTAL_UNDER))
                    continue;
                numRows++;
            }
        }
        
        return numRows;
    }

    public boolean saveLabelToFile(File file){
        
        return saveLabelToFile(file.getPath());
    }
    
    public boolean saveLabelToFile(String path){
        
        try{
            FileOutputStream fs=new FileOutputStream(path);
            ObjectOutputStream os=new ObjectOutputStream(fs);
            os.writeObject(this);
            return true;
        }catch(Exception ex){
            
        }
        
        return false;
    }
    
    public boolean loadLabelFromFile(File file){
    
        return loadLabelFromFile(file.getAbsolutePath());
    }
    
    public boolean loadLabelFromFile(String path){
        try{
            FileInputStream fs=new FileInputStream(path);
            ObjectInputStream os=new ObjectInputStream(fs);
            CLabel temp=(CLabel)os.readObject();
            this.mAddress=temp.mAddress;
            this.mBuilding=temp.mBuilding;
            this.mCity=temp.mCity;
            this.mCompany=temp.mCompany;
            this.mCountry=temp.mCountry;
            this.mName=temp.mName;
            this.mPostalCode=temp.mPostalCode;
            this.mPostalCodePosition=temp.mPostalCodePosition;
            this.mType=temp.mType;
            
            return true;
        }catch(Exception ex){
            JOptionPane.showMessageDialog(null, ex.toString());
        }
        return false;
    }
    
    public static String setFileName(String path){
        String temp=path;
        if(temp.substring(temp.lastIndexOf(".")+1).equals("adr"))
            return temp;
        return temp.concat(".adr");
    }
    
    public int getPostalPosition(){
        return this.mPostalCodePosition;
    }

}
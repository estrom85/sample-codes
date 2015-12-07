/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package labels;

import java.awt.Font;
import java.awt.FontMetrics;
import java.awt.Graphics;
import java.awt.image.BufferedImage;

/**
 *
 * @author martinm
 */
public class LabelMetrics {
    
    private CLabel          label;
    
    private int             width;
    private int             height;
    private int             numOfLines;
    private int             lineSize;
    private Font            font;
    private int             textX;
    private int             textY;
    private int             textWrap;
        
    public LabelMetrics(int width, int height, CLabel label){
        this.width=width;
        this.height=height;
        this.label=label;
        numOfLines=this.numRows();
        setMetrics();
    }
       
    private int numRows(){
        int numRows=0;
        for (int i=0;i<7;i++){
            if(label.isSet(1<<i)){
                if(((1<<i)==CLabel.POSTAL)&&(label.getPostalPosition()!=CLabel.POSTAL_UNDER))
                        continue;
                    numRows++;
                }
            }
        
        return numRows;
    }
        
    public void drawString(int field, Graphics g){
        g.setFont(font);
        String string=label.getField(field);
        //ak dane pole neexistuje, vynechaj ho
        if (string==null)
            return;
        //ak nie je pole nastavene vynechaj ho
        if(!label.isSet(field))
            return;
        //ak sa PSC nenachadza pod mestom, vynechaj ho
        if(field==CLabel.POSTAL&&label.getPostalPosition()!=CLabel.POSTAL_UNDER)
            return;
           
        //ak sa postove smerove cislo nachadza pred alebo za mestom vykresli ho spolu s mestom
        if(field==CLabel.CITY){
            if(label.getPostalPosition()==CLabel.POSTAL_BEFORE)
                string=label.getField(CLabel.POSTAL)+" "+string;
            else if(label.getPostalPosition()==CLabel.POSTAL_AFTER)
                string=string+", "+label.getField(CLabel.POSTAL);
        }
        //ak je text zalomeny rozdeli ho a vykresli na dve riadky
        if((textWrap&field)!=0)
        {
                int textWidth=width-2*textX;//planovana sirka textu
                int i=string.length();
                String temp=string;
                FontMetrics metrics=g.getFontMetrics();
                while(metrics.stringWidth(temp)>textWidth){
                    i=temp.lastIndexOf(" ");
                    temp=temp.substring(0, i);
                }
                g.drawString(temp, textX, textY);
                textY+=lineSize;
                if(i!=string.length()){
                    g.drawString(string.substring(i).trim(), textX, textY);
                    textY+=lineSize;
                }
                
            }
            else{
                g.drawString(string, textX, textY);
                textY+=lineSize;
            }
            
        }

   
    
    private void setMetrics(){
        //nastavi graficky kontext stitku pre ucely merania velkosti
        BufferedImage tmp_img=new BufferedImage(width,height,BufferedImage.TYPE_INT_ARGB);
        Graphics g=tmp_img.getGraphics();
        g.setFont(new Font("Arial",Font.BOLD,25));
        FontMetrics metrics=g.getFontMetrics();
        
        
        double maxWidth=0;
        double maxWidth2=0;
        int maxWidthField=0;
        
        /*
         * nastavi zalomenie textu
         */
        
        //najde nasirsie a druhe najsirsie pole
        for(int i=0;i<7;i++){   //prechadza vsetkymi poliami stitku
            int field=1<<i;     //nastavi pozadovane pole
            if(!label.isSet(field)) //zisti, ci je dane pole zobrazene, ak nie ignoruje ho vo vypoctoch
                continue;
            //zisti sirku pola
            int w=metrics.stringWidth(label.getField(field));
            if(w>maxWidth){         //ak je pole vecie ako aktualne navacsie nastavi ho ako najvecsie
                maxWidth2=maxWidth;
                maxWidth=w;         //ak sa najde vecsie pole ako je aktualne najvecsie nastavi ho ako druhe
                maxWidthField=field; //nastavi najvecie pole
            } 
            else if(w>maxWidth2)    //ak je pole druhe najvecsie nastavi ho
                maxWidth2=w;        //v pripade ak by dane pole ignorovala prva podmienka, testuje ju aj druhykrat
        }
        
        //nastavi textove retazce pre najvecie pole, a odstrihnute pole po zalomeni
        String maxField=label.getField(maxWidthField);
        String wrappedString=maxField;
        
        //zisti ci ma zmysel zalomit text
        if (maxWidth>1.5*maxWidth2){ //len pole ktore je viac ako o polovicu vacsie ako zbytok textu bude
                                     //zalomene
            
            //najde miesto zalomenia textu
            int i=maxField.length();
            while(metrics.stringWidth(maxField.substring(0, i))>
                    maxWidth2){         //kym je text sirsi ako sirka zbytku textu pokracuj v hladani
                wrappedString=maxField.substring(0,i);
                i=wrappedString.lastIndexOf(" ");
            }
            
            //zisti, ci je mozne text zalomit
            //ak by sa po zalomeni malo na druhom riadku ocitnut samotne cislo, nema zmysel text zalomovat
            String temp=maxField.substring(wrappedString.length());
            temp=temp.trim();
            boolean wrappable;
            try{
                Integer.parseInt(temp); //testuje ci sa nachadza v zbytku textu samotne cislo
                wrappable=false;
            }catch(NumberFormatException e){
                wrappable=true;
            }
                
            //upravi pocet riadkov a nastavi pole, ktore bude zalomene ak je zalomenie mozne
            if(wrappable){
                this.numOfLines++;
                this.textWrap=maxWidthField;
                maxField=wrappedString;
            }
            
            
        }
        
        //nastavi velkost pisma v zavislosti od sirky stitku
        int xOffset=10;
        int fontSize=10;
        int w=0;
        //logicky cyklus - upravuje velkost pisma kym, najsirsi text nepresiahne sirku stitku
        while(true){
            this.font=new Font("Arial",Font.BOLD,fontSize);
            metrics=g.getFontMetrics(this.font);
            w=metrics.stringWidth(maxField);
            if(w>this.width-2*xOffset)
                break;
            fontSize++;
            xOffset=fontSize/2;
        }
        
        
        //upravi velkost pisma a vysku riadku v zavislosti od vysky stitka
        
        //logicky cyklus - upravuje velkost pisma, kym nebude text citatelny
        //medzi jednotlivymi poliami stitku musi byt medzera, ktora je velka aspon ako polovica
        //velkosti textu
        while(true){
            //nastavi velkost riadka (velkost pisma+(vyska stitku-pocet riadkov*velkost pisma)/pocet riadkov+2
            //pocet riadkov+2 - berie do uvahy aj priestor na vrchu a spodku stitku (text by sa nemal dotykat okraov
            //stitku)
            this.lineSize=this.font.getSize()+(this.height-
                    (this.numOfLines*this.font.getSize()))/
                    (this.numOfLines+2);
            metrics=g.getFontMetrics(this.font);
            int h=metrics.getAscent()-metrics.getDescent();  
            if(this.lineSize>3*h/2)
                break;
            this.font=new Font("Arial",Font.BOLD,this.font.getSize()-1);
        }
        metrics=g.getFontMetrics(this.font);
        //int h=metrics.getAscent()-metrics.getDescent();
        w=metrics.stringWidth(maxField);
        //nastavi odsadenie od laveho okraju a ukazovatel na prvy riadok
        this.textY=lineSize;
        this.textX=(this.width-w)/2;
        
    }
    
    
}

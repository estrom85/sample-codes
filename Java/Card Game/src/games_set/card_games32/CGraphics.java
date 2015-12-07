/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package games_set.card_games32;

import games_set.games_interface.CSettings;
import java.awt.*;
import java.awt.geom.RoundRectangle2D;
import java.awt.image.BufferedImage;
import java.awt.image.FilteredImageSource;
import java.awt.image.ImageFilter;
import java.awt.image.RGBImageFilter;
import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.imageio.ImageIO;

/**
 *
 * @author mato
 */
public class CGraphics {
    /*
     * Properties
     */
    private BufferedImage           mFrontCard;
    private BufferedImage           mBackCard;
    private BufferedImage           mBackground;
    private BufferedImage           mTypeSet;
    
    private int                     mWidth;
    private int                     mHeight;
    private int                     mCardWidth;
    private int                     mCardHeight;

    /*
     * Constructor
     */
    
    private CGraphics(int width, int height, int cardWidth, int cardHeight){
        if(width==0)
            width=600;
        if(height==0)
            height=450;
        
        mWidth=width;
        mHeight=height;
        mCardWidth=cardWidth;
        mCardHeight=cardHeight;
        
        mFrontCard=null;
        mBackCard=null;
        mBackground=null;
        mTypeSet=null;
        
    }
    
    /**
     * creates instance of class, initialize components ands sets all
     * images.
     * 
     * @param width width of display
     * @param height height of display
     * @param cardWidth width of card
     * @param cardHeight height of card
     * @return instatnce of class
     */
    public static CGraphics createGraphics(int width, int height, int cardWidth, int cardHeight){
        CGraphics output=new CGraphics(width, height, cardWidth, cardHeight);
        /*
        output.setFrontCardSet(null);
        output.setBackground(null);
        output.setBackCard(null);
        output.setTypeSet(null);
        */
        
        return output;
    }

    /**
     * Create instatce of class with default card size
     * @param width
     * @param height
     * @return 
     */
    public static CGraphics createGraphics(int width, int height){
        return createGraphics(width, height, 65, (int)(65*1.66));
    }
    
     private BufferedImage setTransparent(Image img, Color rgb1, Color rgb2){
        final int r1=rgb1.getRed();
        final int r2=rgb2.getRed();
        final int g1=rgb1.getGreen();
        final int g2=rgb2.getGreen();
        final int b1=rgb1.getBlue();
        final int b2=rgb2.getBlue();
        
        //reads imput image from url
        
        if(img==null)
            return null;
        
        
        //creates filter that sets up transparency for every color
        ImageFilter filter=new RGBImageFilter() {

            @Override
            public int filterRGB(int x, int y, int rgb) {
                
                //extract red, green and blue component from rgb variable
                //of ARGB type
        
                int r=(rgb&0xFF0000)>>16;
                int g=(rgb&0xFF00)>>8;
                int b=(rgb&0xFF);
                
                //test for filtered color
                boolean transparent=
                        (r1<=r&&r<=r2)&&
                        (g1<=g&&g<=g2)&&
                        (b1<=b&&b<=b2);
                
                if(transparent)
                    //filteres color. sets alpha component to 0;
                    return rgb&0x00FFFFFF;
                
                return rgb;
            }
        };
        
        //variable to accept to public methods of class component
        Component toolkit=new Component() {};
        
        //creates filtered image using created filter
        Image image=toolkit.createImage(new FilteredImageSource(img.getSource(),filter));
        //creates output image
        
        BufferedImage output=new BufferedImage(img.getWidth(null),img.getHeight(null),BufferedImage.TYPE_INT_ARGB);
        Graphics outputGraphics=output.getGraphics();
        outputGraphics.drawImage(image, 0, 0, null);
        
        return output;
    }
    
     //reads image from url
    private BufferedImage readImg(String source){
        URL url;
        try {
            url = new URL(CSettings.getURL()+"/Images/"+source);
        } catch (MalformedURLException ex) {
            Logger.getLogger(CGraphics.class.getName()).log(Level.SEVERE, null, ex);
            return null;
        }
        BufferedImage temp;
        try {
            temp= ImageIO.read(url);
        } catch (IOException ex) {
            Logger.getLogger(CGraphics.class.getName()).log(Level.SEVERE, null, ex);
            temp=null;
        }
        return temp;
    }
    
    /**
     * Returns outline of card
     * @param x x position of card
     * @param y y position of card
     * @param offset outline thicknes
     * @return returns shape of outline
     */
    public Shape getCardOutline(int x, int y, int offset){
        return new RoundRectangle2D.Double(x-offset,y-offset,mCardWidth+2*offset,mCardHeight+2*offset, 15, 15);
    }
    
    public Shape getCardOutline(int x, int y){
        return getCardOutline(x,y,0);
    }
    
    /**
     * Sets front card image set from url.
     * If url is null or invalid, renders default set of cards.
     * They are ugly but it is your problem.
     * 
     * @param url url of image
     */
    public void setFrontCardSet(URL url){
        mFrontCard=CGraphicsUtils.createFrontCardSet(url,this);
    }
    /**
     * Sets front card image set from url.
     * If url is null or invalid, renders default set of cards.
     * They are ugly but it is your problem. Sets colors between
     * rgb1 and rgb2 transparent.
     * @param url
     * @param rgb1
     * @param rgb2 
     */
    public void setFrontCardSet(URL url, Color rgb1, Color rgb2){
        mFrontCard=setTransparent(CGraphicsUtils.createFrontCardSet(url,this),
                rgb1,rgb2);
    }
    /**
     * Returns image of selected card
     * @param card
     * @param type
     * @return 
     */
    public Image getFrontCard(int card, int type){
        //if front card is not set, create default set
        if(this.mFrontCard==null)
            this.setFrontCardSet(null);
        
        BufferedImage output=new BufferedImage(mCardWidth,mCardHeight,BufferedImage.TYPE_INT_ARGB);
        Graphics2D g=output.createGraphics();
        g.drawImage(mFrontCard, -card*mCardWidth, -type*mCardHeight, null);
        return output;
    }
    
    /**
     * Sets back card image set from url.
     * If url is null or invalid, renders default set of cards.
     * They are ugly but it is your problem.
     * 
     * @param url url of image
     */
    public void setBackCard(URL url){
        mBackCard=CGraphicsUtils.createBackCard(url, this);
    }
    /**
     * Sets back card image set from url.
     * If url is null or invalid, renders default set of cards.
     * They are ugly but it is your problem. Sets colors between
     * rgb1 and rgb2 transparent.
     * @param url
     * @param rgb1
     * @param rgb2 
     */
    public void setBackCard(URL url, Color rgb1, Color rgb2){
        mBackCard=setTransparent(CGraphicsUtils.createBackCard(url, this),
                rgb1,rgb2);
    }
   
    /**
     * Returns image of back of card
     * @return 
     */
    public Image getBackCard(){
        if(this.mBackCard==null)
            this.setBackCard(null);
        return mBackCard;
    }
    
    /**
     * Sets background image set from url.
     * If url is null or invalid, renders default set of cards.
     * They are ugly but it is your problem.
     * 
     * @param url url of image
     */
    public void setBackground(URL url){
        mBackground=CGraphicsUtils.createBackground(url,this);
    }
    /**
     * Sets background image set from url.
     * If url is null or invalid, renders default set of cards.
     * They are ugly but it is your problem. Sets colors between
     * rgb1 and rgb2 transparent.
     * 
     * @param url url of image
     */
    public void setBackground(URL url, Color rgb1, Color rgb2){
        mBackground=setTransparent(CGraphicsUtils.createBackground(url,this),
                rgb1, rgb2);
    }
   
    public Image getBackground(){
        if(this.mBackground==null)
            this.setBackground(null);
        return mBackground;
    }
    /**
     * Sets Type set image set from url.
     * If url is null or invalid, renders default set of cards.
     * They are ugly but it is your problem.
     * 
     * @param url url of image
     */
    public void setTypeSet(URL url){
        mTypeSet=CGraphicsUtils.createTypeSet(url, this);
    }
    /**
     * Sets background image set from url.
     * If url is null or invalid, renders default set of cards.
     * They are ugly but it is your problem. Sets colors between
     * rgb1 and rgb2 transparent.
     * 
     * @param url url of image
     */
    public void setTypeSet(URL url, Color rgb1, Color rgb2){
        mTypeSet=setTransparent(CGraphicsUtils.createTypeSet(url, this),
                rgb1, rgb2);
    }
    
    public Image getType(int type){
        if(this.mTypeSet==null)
            this.setTypeSet(null);
        BufferedImage output=new BufferedImage(mCardWidth,mCardWidth,BufferedImage.TYPE_INT_ARGB);
        Graphics2D g=output.createGraphics();
        g.drawImage(mTypeSet, -type*mCardWidth, 0, null);
        return output;
    }
    
    public int cardWidth(){
        return mCardWidth;
    }
    
    public int cardHeight(){
        return mCardHeight;
    }
    
    public int displayWidth(){
        return mWidth;
    }
    
    public int displayHeight(){
        return mHeight;
    }
           
   
}

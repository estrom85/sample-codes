/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package games_set.card_games32;

import java.awt.Color;
import java.awt.Font;
import java.awt.Graphics2D;
import java.awt.Image;
import java.awt.image.BufferedImage;
import java.io.IOException;
import java.net.URL;
import javax.imageio.ImageIO;

/**
 *
 * @author mato
 */
public class CGraphicsUtils {
    private CGraphicsUtils(){}
    
    
    
    /**
     * Reads image from url.
     * If url is invalid or reading fails returns null.
     * @param url
     * @return 
     */
    private static BufferedImage readImage(URL url){
        BufferedImage input;
        
        //starting conditions: if url does not exists or image cant read render cards
        if(url==null)
            input=null;
        else{
            try {
                input=ImageIO.read(url);
            } catch (IOException ex) {
                input=null;
            }
        }
        return input;
    }
    
    /**
     * Renders image from URL.
     * If url is invalid or NULL returns rendered image.
     * Else takes image scales it adds alpha and returns ARGB image usable for displaying.
     * 
     * 
     * @param url URL of image
     * @return card set
     */
    static BufferedImage createFrontCardSet(URL url,CGraphics g){
        BufferedImage input=readImage(url);

        //creates output buffer for image
        BufferedImage output=new BufferedImage(g.cardWidth()*8,g.cardHeight()*4,BufferedImage.TYPE_INT_ARGB);
        Graphics2D graphics=output.createGraphics();
        
        //draws transparent background
        graphics.setColor(new Color(255,255,255,0));
        graphics.drawRect(0, 0, output.getWidth(), output.getHeight());
        
        int width=0;
        int height=0;
        
        if(input!=null)
        {
            width=input.getWidth()/8;
            height=input.getHeight()/4;
        }
        
        //main drawing loop, takes card from source scales it clips and draws into
        //output image
        for(int type=0;type<4;type++)
        {
            for(int card=0;card<8;card++)
            {
                //draws outline
                if(input==null)
                    renderFrontCard(graphics,card,type, g);
                else
                    renderFrontCard(graphics,card,type,g, input);
            }
        }
        return output;
    }
    /**
     * Renders card from image
     * 
     * @return card set
     */
    private static void renderFrontCard(Graphics2D g, int card, int type, 
            CGraphics g_src, BufferedImage img){
        
        int width=img.getWidth()/8;
        int height=img.getHeight()/4;
        int src_x=card*width;
        int src_y=type*height;
        int dest_x=card*g_src.cardWidth();
        int dest_y=type*g_src.cardHeight();
        
        //draws outline
        g.setColor(Color.GRAY);       
        g.fill(g_src.getCardOutline(dest_x, dest_y));    
        //clips outline of card and draws card from image
        g.setClip(g_src.getCardOutline(dest_x, dest_y,-1));       
       
        g.drawImage(img, dest_x, dest_y, dest_x+g_src.cardWidth(), dest_y+g_src.cardHeight(),
                        src_x, src_y, src_x+width, src_y+height
                        ,null);
        
        g.setClip(null);
    
    }
    /**
     * Renders card
     * @param g
     * @param card
     * @param type 
     */
    private static void renderFrontCard(Graphics2D g, int card, int type,
            CGraphics g_src){

        int cardWidth=g_src.cardWidth();
        int cardHeight=g_src.cardHeight();
        int x=card*cardWidth;
        int y=type*cardHeight;
        String[] numbers={"VII","VIII","IX","X","J","Q","K","A"};
        //draws outline
        g.setColor(Color.GRAY);
        g.fill(g_src.getCardOutline(x,y));
        //fills card white
        g.setColor(Color.WHITE);
        g.fill(g_src.getCardOutline(x,y,-1));
        
        //sets color of card
       
        g.setColor(Color.BLACK);
        
        //sets font and draws number
        g.setFont(new Font("Arial",Font.BOLD,20));
        
        Image temp = null;
        //draws type of card
        switch(type)
        {
            //heart
           case 0:
                temp=CDrawingUtils.drawHeart(cardWidth/2);
                break;
            case 1:
                temp=CDrawingUtils.drawLeaf(cardWidth/2);
                break;
            case 2:
                temp=CDrawingUtils.drawSphere(cardWidth/2);
                break;
             case 3:
                temp=CDrawingUtils.drawAcorn(cardWidth/2);
        }
        
                g.drawString(numbers[card], x+5, y+25);
                g.drawString(numbers[card], x+cardWidth-25, y+cardHeight-5);
                g.drawImage(temp, x+cardWidth/4, y+cardHeight/2-cardWidth/4,null);

 
    }

    /**
     * Renders image from URL.
     * If url is invalid or NULL returns rendered image.
     * Else takes image scales it adds alpha and returns ARGB image usable for displaying.
     * 
     * 
     * @param url URL of image
     * @return card set
     */
    static BufferedImage createBackCard(URL url, CGraphics g){
        BufferedImage input=readImage(url);
        
        BufferedImage output=new BufferedImage(g.cardWidth()+2,g.cardHeight()+2,BufferedImage.TYPE_INT_ARGB);
        Graphics2D graphics=output.createGraphics();
        
        graphics.setColor(new Color(255,255,255,0));
        graphics.fillRect(0, 0, output.getWidth(), output.getHeight());
        
        if(input==null)
            renderBackCard(graphics,g);
        else
            renderBackCard(graphics,g, input);
        
        return output;
        
    }

    private static void renderBackCard(Graphics2D g, CGraphics g_src, BufferedImage img){
        //draws outline
        g.setColor(Color.GRAY);       
        g.fill(g_src.getCardOutline(1, 1,1));    
        //clips outline of card and draws card from image
        g.setClip(g_src.getCardOutline(1, 1));       
       
        g.drawImage(img, 1, 1, 1+g_src.cardWidth(), 1+g_src.cardHeight(),
                        0, 0, img.getWidth(), img.getHeight(),
                        null);
        
        g.setClip(null);
    }
    
    private static void renderBackCard(Graphics2D g, CGraphics g_src){

        g.setColor(Color.GRAY);
        g.fill(g_src.getCardOutline(1,1,1));
        g.setColor(Color.CYAN);
        g.fill(g_src.getCardOutline(1,1));
        g.setColor(Color.DARK_GRAY);
        
        int width=g_src.cardWidth()/10;
        
        
        for (int i=1;i<10;i+=2)
        {
            for (int j=1;j<16;j+=2)
            {
                g.fillRect(width*i, width*j+1, width, width);
            }
        }
    }

    /**
     * Renders image from URL.
     * If url is invalid or NULL returns rendered image.
     * Else takes image scales it adds alpha and returns ARGB image usable for displaying.
     * 
     * 
     * @param url URL of image
     * @return card set
     */
    static BufferedImage createBackground(URL url, CGraphics g){
        BufferedImage input=readImage(url);
        
        BufferedImage output=new BufferedImage(g.displayWidth(), g.displayHeight(),
                BufferedImage.TYPE_INT_ARGB);
        Graphics2D graphics=output.createGraphics();
        
        if(input==null)
            renderBackground(graphics,g);
        else
            renderBackground(graphics,g, input);
        
        return output;
    }
    
    private static void renderBackground(Graphics2D g, CGraphics g_src, BufferedImage img){
        int rx=(int)Math.ceil((double)g_src.displayWidth()/
                (double)img.getWidth());
        int ry=(int)Math.ceil((double)g_src.displayHeight()/
                (double)img.getHeight());
        
        for(int x=0;x<rx;x++)
        {
            for(int y=0;y<ry;y++)
            {
                g.drawImage(img, x*img.getWidth(), y*img.getHeight(), null);
            }
        }
    }
    
    private static void renderBackground(Graphics2D g, CGraphics g_src){
        int w=5;
        int rx=(int)Math.ceil((double)g_src.displayWidth()/w);
        int ry=(int)Math.ceil((double)g_src.displayHeight()/w);
        boolean first=true;
        boolean current=first;
        
        for(int x=0;x<rx;x++)
        {
            for(int y=0;y<ry;y++)
            {
                if(current)
                    g.setColor(Color.GREEN);
                else
                    g.setColor(Color.CYAN);
                
                g.fillRect(x*w, y*w, w, w);
                current=!current;
            }
            first=!first;
            current=first;
        }
        
    }

    
    static BufferedImage createTypeSet(URL url, CGraphics g){
        BufferedImage input=readImage(url);
        
        BufferedImage output=new BufferedImage(g.cardWidth()*4, g.cardWidth(),
                BufferedImage.TYPE_INT_ARGB);
        Graphics2D graphics=output.createGraphics();
        graphics.setColor(new Color(255,255,255,0));
        graphics.fillRect(0, 0, output.getWidth(), output.getHeight());
        
        for(int i=0;i<4;i++)
        {
            if(input==null)
                renderTypeSet(graphics,g,i);
            else
                renderTypeSet(graphics,g,i,input);
        }
        
        return output;
    }
    
    private static void renderTypeSet(Graphics2D g, CGraphics g_src, int type, BufferedImage img){
        int src_x=(img.getWidth()/4)*type;
        int src_x2=(img.getWidth()/4)*(type+1);
        
        int dest_x=g_src.cardWidth()*type;
        int dest_x2=g_src.cardWidth()*(type+1);
        
        g.drawImage(img, dest_x, 0, dest_x2, g_src.cardWidth(), src_x, 0, src_x2, img.getHeight(), null);
    }

    private static void renderTypeSet(Graphics2D g, CGraphics g_src, int type){
        Image temp = null;
        switch(type)
        {
            case 0:
                temp=CDrawingUtils.drawHeart(g_src.cardWidth());
                break;
            case 1:
                temp=CDrawingUtils.drawLeaf(g_src.cardWidth());
                break;
            case 2:
                temp=CDrawingUtils.drawSphere(g_src.cardWidth());
                break;
            case 3:
                temp=CDrawingUtils.drawAcorn(g_src.cardWidth());
        }
        g.drawImage(temp, type*g_src.cardWidth(), 0, null);
    }
}

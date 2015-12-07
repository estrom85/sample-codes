/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package games_set;
import games_set.games_interface.CGameManager;
import java.awt.Canvas;
import java.awt.Color;
import java.awt.Graphics;
import java.awt.Image;
import java.awt.image.BufferedImage;


/**
 *
 * @author martinm
 */
public class CDisplay extends Canvas {
    
    Image bufferedImage;
    Graphics backBuffer;
    int width;
    int height;
    
    boolean doubleBuffering;
    
    public CDisplay()
    {
        super();
        bufferedImage=null;
        backBuffer=null;
        doubleBuffering=false;
    }
    
    public void enableDoubleBuffering(boolean i)
    {
        doubleBuffering=i;
    }
    
    /* 
     * hlavna zobrazovacia funkica. Ak je hra spustena, preda zobrazovanie
     * hre. inak zobrazi zelenu obrazovku
     */
    @Override
    public void paint(Graphics d)
    {
        
        Graphics graphics;
        if(doubleBuffering)
        {
        
            if(width!=getWidth()||height!=getHeight()||
                    bufferedImage==null||backBuffer==null)
                resetBuffer();
            graphics=backBuffer;
        }
        else graphics=d;
        
        if(CGameManager.gameInitialized())
           CGameManager.onRender(graphics);
        else
        {
            graphics.setColor(Color.GREEN);
            graphics.fillRect(0, 0, this.getWidth(), this.getHeight());
        }
        
        if(doubleBuffering)
            d.drawImage(bufferedImage, 0, 0, null);
        
    }
    @Override
    public void update(Graphics g)
    {
        if(doubleBuffering)
            paint(g);
        else
            super.update(g);
    }

    private void resetBuffer() {
        width=getWidth();
        height=getHeight();
        
        if(bufferedImage!=null)
            bufferedImage.flush();
        if(backBuffer!=null)
            backBuffer.dispose();
        
        bufferedImage=new BufferedImage(width,height,BufferedImage.TYPE_INT_RGB);
        backBuffer=bufferedImage.getGraphics();
        
    }
    
}

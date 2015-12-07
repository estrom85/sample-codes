/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package games_set.card_games32;

import java.awt.Color;
import java.awt.Font;
import java.awt.Graphics;

/**
 *
 * @author mato
 */
abstract public class CMenuItem {
    private String itemField;
    
    private int x_pos,y_pos,width,height;
    
    public CMenuItem(String name){
        x_pos=0;
        y_pos=0;
        width=0;
        height=0;
        itemField=name;
    }
    
    abstract public void execute();
    
    public void setSize(int w, int h){
        width=w;
        height=h;
    }
    
    public void setPosition(int x, int y){
        x_pos=x;
        y_pos=y;
    }
    
    public void mouseClicked(int mouseX, int mouseY){
        if(mouseOn(mouseX,mouseY))
            execute();
    }
    
    public void renderButton(Graphics g, int mouseX, int mouseY){
        g.setColor(Color.BLACK);
        g.fillRoundRect(x_pos, y_pos, width, height,20,20);
        if(mouseOn(mouseX,mouseY))
            g.setColor(Color.YELLOW);
        else
            g.setColor(new Color(0xD6,0xAD,0x33));
        g.fillRoundRect(x_pos+2, y_pos+2, width-4, height-4,20,20);
        g.setColor(Color.BLACK);
        g.setFont(new Font("Arial",Font.BOLD|Font.ITALIC,height-10));
        int x=x_pos+width/2-g.getFontMetrics().stringWidth(itemField)/2;
        int y=y_pos+height-7;
        g.drawString(itemField, x, y);
        
    }
    
    private boolean mouseOn(int mouseX, int mouseY){
        if((mouseX>x_pos&&mouseX<(x_pos+width))&&(mouseY>y_pos&&mouseY<(y_pos+height)))
            return true;
        return false;
    }
}

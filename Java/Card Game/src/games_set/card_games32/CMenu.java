/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package games_set.card_games32;

import java.awt.Color;
import java.awt.Graphics;
import java.util.Iterator;
import java.util.Stack;

/**
 *
 * @author mato
 */
public class CMenu {
    private Stack<CMenuItem> mMenuItems;
    private CGraphics g_src;
    
    public CMenu(CGraphics graphics){
        mMenuItems=new Stack();
        g_src=graphics;
    }
    
    public void addMenuItem(CMenuItem item){
        int width=(int) (g_src.displayWidth()*0.6);
        int height=35;
        item.setSize(width, height);
        mMenuItems.push(item);
        setPositions();
    }
    
    public void renderMenu(Graphics g, int mouseX, int mouseY){
        g.setColor(new Color(88,88,88,180));
        g.fillRect(0, 0, g_src.displayWidth(), g_src.displayHeight());
        
        Iterator<CMenuItem> iterator=mMenuItems.iterator();
        while(iterator.hasNext()){
            CMenuItem temp=iterator.next();
            temp.renderButton(g, mouseX, mouseY);
        }
       
        
        /*
        for(int i=0;i<mMenuItems.size();i++)
            mMenuItems.get(i).renderButton(g, mouseX, mouseY);
        */
    }
    
    public void onClick(int mouseX, int mouseY){
        Iterator<CMenuItem> iterator=mMenuItems.iterator();
        while(iterator.hasNext()){
            CMenuItem temp=iterator.next();
            temp.mouseClicked(mouseX, mouseY);
        }
    }
    
    public void setPositions(){
        if(mMenuItems.size()==0)
            return;
        int offset=g_src.displayHeight()/(mMenuItems.size()+1);
        int x=(int)(g_src.displayWidth()-g_src.displayWidth()*0.6)/2;
        int i=0;
        Iterator<CMenuItem> iterator=mMenuItems.iterator();
        while(iterator.hasNext()){
            CMenuItem temp=iterator.next();
            temp.setPosition(x, offset*(i+1)-17);
            i++;
        }
        
    }
    
}

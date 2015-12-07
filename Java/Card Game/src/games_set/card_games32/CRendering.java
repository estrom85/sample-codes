
package games_set.card_games32;


import games_set.card_games32.sedma3.CHumanPlayer;
import java.awt.*;
import java.awt.geom.RoundRectangle2D;

/**
 *
 * @author mato
 */

/**
 * Utility class that will care of Rendering the play field
 */
public class CRendering {
    
    /*
     * disables instatiation of class. This class consists of static methods only
     * so is not necesarry to make instances of this class
     */
    
    private CRendering(){}
    
    private static class Point{
        int x;
        int y;
        
        public Point(int x, int y){
            this.x=x;
            this.y=y;
        }
        public Point(){
            this(0,0);
        }
        
    }
    
    /*
     * Rendering methods
     */
    
    /**
     * Renders player hand
     * 
     * @param g - refernce to class Graphics that takes care of displaying play field
     * @param graphicSource - refernce to class that holds rendering information
     * @param player - reference to human player 
     * @param mouseX - Xposition of mouse
     * @param mouseY - Yposition of mouse
     * @param width - width of display
     * @param height  - height of display
     */
    
    
    public static void renderPlayerHand(Graphics g,CGraphics g_src, CPlayer player, 
            int mouseX, int mouseY, boolean active){
        
        int numOfCards=player.getNumberOfCards();
        //sets space between each card. Distance between first and last card has to be maximum 0.65 of display width      
        int offset=(int)((0.65*g_src.displayWidth())/(numOfCards-1));
        //position indicators - stores info about position of card
        int y;
        int x;
        //temporary stores image of card before rendering
        Image temp;
        //index of card on which is mouse on
        
        int i=-1;
        if(active)
            i=getCardIndex(player,g_src,mouseX,mouseY);
        //position of card on which mouse is on
        int x2=0;
        int y2=0;
        
        //displays cards on field
        for(int j=0;j<numOfCards;j++)
        {
            //gets card position
            x=(int)((0.35/2)*g_src.displayWidth()-g_src.cardWidth()/2+offset*j);
            y=(int)(0.95*g_src.displayHeight()-g_src.cardHeight());
            /*
            if(g_src.getCardOutline(x, y).contains(mouseX, mouseY)){
                x2=x;
                y2=y;
                i=j;
              //  continue;
            }
            */
            
            //skips rendering of card, which has mouse on and stores card position
            if(i==j){
                x2=x;
                y2=y;
                continue;
            }
            
            //renders card
            temp=g_src.getFrontCard(player.getCard(j).getNumber(), player.getCard(j).getType());
            g.drawImage(temp, x, y, null);
            
        }
        
        //renders card, which has mouse on it
        if(i>-1&&i<50)
        {
            //sets and renders shadow
            g.setColor(new Color(88,88,88,120));
            ((Graphics2D)g).fill(g_src.getCardOutline(x2+4, y2+4));
            //renders card which is position bit above all others cards
            temp=g_src.getFrontCard(player.getCard(i).getNumber(), player.getCard(i).getType());
            g.drawImage(temp, x2-2, y2-2, null);
        }
        //prints out information about player hand
        x=(int)((0.35/2)*g_src.displayWidth()-g_src.cardWidth()/2);
        y=(int)(0.95*g_src.displayHeight()-g_src.cardHeight())-15;
        String karta;
        switch(player.getNumberOfCards())
        {
            case 1:
                karta="karta";
                break;
            case 2:
            case 3:
            case 4:
                karta="karty";
                break;
            default:
                karta="kariet";
                break;
        }
        g.setColor(Color.BLACK);
        g.setFont(new Font("Arial",Font.BOLD,15));
        g.drawString(player.name()+": "+player.getNumberOfCards()+" "+karta, x, y);
    }
    
    public static void renderPlayerHand(Graphics g,CGraphics g_src, CPlayer player, 
            int mouseX, int mouseY){
        renderPlayerHand(g,g_src,player,mouseX,mouseY,true);
    }
    /**
     * Renders other players hand
     * 
     * @param g - reference to graphic content
     * @param graphicSource - reference to class that holds rendering information
     * @param player - reference to other player
     * @param numOfPlayers - total number of players around the table
     * @param width - width of display
     * @param height  - height of display
     */
    public static void renderOtherPlayerHand(Graphics g, CGraphics g_src, CPlayer player,
            int numOfPlayers){
        Point position=getPlayerPosition(player.index(),g_src,numOfPlayers);
        int numOfCards=player.getNumberOfCards();
        int width=g_src.cardWidth()*(int)(1+0.25*(numOfCards-1));
        int x=position.x+width/2-g_src.cardWidth();
        int y=position.y+g_src.cardHeight()/2;
        Image img=g_src.getBackCard();
        for(int i=0;i<numOfCards;i++)
        {
            g.drawImage(img, x-(int)(g_src.cardWidth()*0.25*i), y, null);
        }
        g.setColor(Color.BLACK);
        g.setFont(new Font("Arial",Font.BOLD,15));
        String player_name=player.name()+" ("+player.getNumberOfCards()+")";
        FontMetrics fontSize=g.getFontMetrics();
        int stringPos=position.x-fontSize.stringWidth(player_name)/2;
        //g.drawString(player_name, position.x-width, y-15);
        g.drawString(player_name, stringPos, y-15);
        
    }

    /**
     * Renders game table
     * 
     * @param g - reference to graphic content
     * @param graphicSource - reference to class that contains rendering information
     * @param deck - refernce to deck
     * @param trash  - reference to trash
     * @param width - width of display 
     * @param height - height of display
     */
    public static void renderTable(String gameName, Graphics g, CGraphics g_src, CPacket deck, CPacket trash,
            int mouseX, int mouseY,boolean active, boolean activeTrash){
        Graphics2D g2=(Graphics2D)g;
        Point temp;
        Image img;
        
        int cardIndex=getCardIndex(null,g_src,mouseX,mouseY);
        
        //draw background
        g.drawImage(g_src.getBackground(), 0, 0, null);
        //draw name of game
        g.setColor(Color.WHITE);
        g.setFont(new Font("Arial",Font.BOLD|Font.ITALIC,25));
        FontMetrics metrics=g.getFontMetrics();
        g.drawString(gameName, g_src.displayWidth()/2-metrics.stringWidth(gameName)/2, 30);
        //draw table
        g.drawOval(15, 45, g_src.displayWidth()-30, g_src.displayHeight()-75);
        g.setColor(new Color(0,0,0,50));
        g.fillOval(15, 45, g_src.displayWidth()-30, g_src.displayHeight()-75);
        
        //draw decks
        temp=getDeckPosition(g_src,'d'); //save deck coords
        if(deck.isEmpty()){  
            //draws empty deck
            g.setColor(new Color(88,88,88,125));  
            g2.fill(g_src.getCardOutline(temp.x, temp.y));  
        }  
        else{  
            //temporary stores back card
            img=g_src.getBackCard();
            //gets number of cards in deck
            int numDeckCards=deck.size();
            //if mouse is on the deck moves upmost card bit upward
            //Debug.println("cardIndex", Integer.toString(cardIndex));
            if (cardIndex==100&&active)
                numDeckCards-=1;//if mouseover num of cards rendered in deck is 1 less
            //draws deck
            for(int i=0;i<numDeckCards;i++)  
                g.drawImage(img, temp.x-(i-deck.size())/3, temp.y-(i-deck.size())/3, null);
                     
            //if(g_src.getCardOutline(temp.x,temp.y).contains(mouseX, mouseY))
            if(cardIndex==100&&active){ 
                //draws mouseover card
                g.setColor(new Color(88,88,88,120));                 
                g2.fill(g_src.getCardOutline(temp.x+4, temp.y+4));                 
                g.drawImage(img, temp.x-2, temp.y-1, null);             
            }         
        }
        //draws trash
        temp=getDeckPosition(g_src,'t');
        
        if(trash.isEmpty()){  
            g.setColor(new Color(88,88,88,125));
            g2.fill(g_src.getCardOutline(temp.x+deck.size()/3, temp.y+deck.size()/3));
        }
        else{
            CCard topCard=trash.getTopCard();
            img=g_src.getFrontCard(topCard.getNumber(), topCard.getType());
            //gets number of cards in deck
            int numDeckCards=trash.size();
            //if mouse is on the deck moves upmost card bit upward
            //Debug.println("cardIndex", Integer.toString(cardIndex));
            if (cardIndex==200&&activeTrash)
                numDeckCards-=1;//if mouseover num of cards rendered in deck is 1 less
            //draws deck
            for(int i=0;i<numDeckCards;i++)      
                g.drawImage(img,temp.x-(i-trash.size())/3,                 
                    temp.y-(i-deck.size())/3, null);
            if(cardIndex==200&&activeTrash){ 
                //draws mouseover card
                g.setColor(new Color(88,88,88,120));                 
                g2.fill(g_src.getCardOutline(temp.x+4, temp.y+4));                 
                g.drawImage(img, temp.x-2, temp.y-1, null);             
            } 
        }
        
    }
    
    public static void renderTable(String gameName, Graphics g, CGraphics g_src, CPacket deck, CPacket trash,
            int mouseX, int mouseY){
        renderTable(gameName, g,g_src,deck,trash,mouseX,mouseY,true,false);
    }
    /**
     * Displays card type when needed
     * 
     * @param g - reference to graphic content
     * @param graphicSource - reference to class that contains informations about rendering
     * @param type - card type
     * @param mouseX - X position of mouse
     * @param mouseY - Y position of mouse
     * @param width - width of display 
     * @param height  - height of display
     */
    public static void displayCardType(Graphics g, CGraphics g_src, 
            int type){
        Image img=g_src.getType(type);
        Point deckPos=getDeckPosition(g_src, 'd');
        Point trashPos=getDeckPosition(g_src, 't');
        //get outline in between deck and trash
        int left=deckPos.x+g_src.cardWidth();
        int right=trashPos.x;
        int top=deckPos.y;
        int bottom=top+g_src.cardHeight();
        
        //adjust indicator box to square with size of 0.8 of space between decks
        int width=(int)((double)(right-left)*0.6);
        int offsetX=((right-left)-width)/2;
        int offsetY=((bottom-top)-width)/2;
        
        left+=offsetX;
        right-=offsetX;
        top+=offsetY;
        bottom-=offsetY;
        
        g.drawImage(img, left, top, right, bottom, 0, 0, img.getWidth(null), img.getHeight(null), null);
        
    }

    /**
     * Wrapper method to display whole table. 
     * This method is made out of
     * convinience so user doesn't need to call all these method.
     * Its up to programmer how he will render playfield
     * 
     * @param g - refernce to graphic content
     * @param graphicSource - reference to class which holds informations about rendering
     * @param players - array of all players
     * @param deck - reference to deck of card
     * @param trash - refernce to trash deck
     * @param mouseX - X position of mouse
     * @param mouseY - Y position of mouse
     * @param width - width of display
     * @param height - height of display
     */
    public static void renderPlayField(String gameName, Graphics g, CGraphics graphicSource, CPlayer[] players,
            CPacket deck, CPacket trash, int mouseX, int mouseY){
        //renders table
        renderTable(gameName, g,graphicSource,deck,trash,mouseX, mouseY);
        //renders player hands
        for(int i=0;i<players.length;i++)
        {
            if(players[i]==null) continue;
            
            if(players[i] instanceof CHumanPlayer)
                renderPlayerHand(g,graphicSource,players[i],mouseX,mouseY);
            else
                renderOtherPlayerHand(g,graphicSource,players[i],players.length);
        }
    }
    
    /**
     * Renders card type selection if needed
     * 
     * @param g - refernce to graphic content 
     * @param g_src - reference to class which holds informations about rendering
     * @param mouseX - X position of mouse
     * @param mouseY - Y position of mouse
     * @param width - width of display
     * @param height - height of display
     */
    public static void renderSelectType(Graphics g, CGraphics g_src,
            int mouseX, int mouseY){
        g.setColor(new Color(88,88,88,180));
        g.fillRect(0, 0, g_src.displayWidth(), g_src.displayHeight());
        Image temp=null;
        
       
        String label="Vyber farbu";
        g.setFont(new Font("Arial",Font.BOLD|Font.ITALIC,40));
        FontMetrics metrics=g.getFontMetrics();
        g.setColor(Color.RED);
        g.drawString(label, g_src.displayWidth()/2-metrics.stringWidth(label)/2, g_src.displayHeight()/4+metrics.getHeight());
        
        for(int i=0;i<4;i++){
            
            RoundRectangle2D.Double outline=getTypeOutline(g_src,i);
            boolean mouseon=outline.contains(mouseX, mouseY);
            temp=g_src.getType(i);
            int x=(int)outline.getX();
            int y=(int)outline.getY();
            int width=(int)outline.getWidth();
            g.setColor(new Color(0x6C,0x43,0x19));
            g.fillRoundRect(x-5, y-5, width+10, width+10, 10, 10);
            g.setColor(new Color(0xFF,0xFF,0xB8));
            g.fillRoundRect(x, y, width, width, 10, 10);
            g.drawImage(temp, x+5, y+5, x+width-5, y+width-5, 0, 0, temp.getWidth(null), temp.getHeight(null), null);
            if(!mouseon){
                g.setColor(new Color(88,88,88,120));
                g.fillRoundRect(x, y, width, width, 10, 10);
                
            }
            
        }
    }
    
    
    /*
     * Return methods
     */
    
    /**
     * Based on rendering information returns card index selected
     * 
     * @param player - refernce to player
     * @param event - reference to mouse event handler
     * @return index of card depending on mouse position
     */
    public static int getCardIndex(CPlayer player, CGraphics g_src, int mouseX, int mouseY){
        
        Point mouse=new Point(mouseX,mouseY);
        Point pos;

        pos=getDeckPosition(g_src,'d');
        
        if(g_src.getCardOutline(pos.x,pos.y).contains(mouse.x, mouse.y))
            return 100;
        
        pos=getDeckPosition(g_src,'t');
        if(g_src.getCardOutline(pos.x, pos.y).contains(mouse.x, mouse.y))
            return 200;
       
        if(player==null)
            return -1;
        
        /*
       
        int j=((T_CHumanPlayer)mPlayers[0]).selected();
        
        if(j>-1)
            if(T_CGraphics.getCardOutline(playerCardsCoords[j][0], playerCardsCoords[j][1]-25)
                    .contains(x, y))
                return j;
        */
        int offset=(int)((0.65*g_src.displayWidth())/(player.getNumberOfCards()-1));
        for(int i=player.getNumberOfCards()-1;i>-1;i--)
        {
            int x=(int)((0.35/2)*g_src.displayWidth()-g_src.cardWidth()/2+offset*i);
            int y=(int)(0.95*g_src.displayHeight()-g_src.cardHeight());
            if(g_src.getCardOutline(x, y)
                    .contains(mouse.x, mouse.y))
                return i;
        }
        
        return -1;
    }

    /**
     * returns selected type when type change is needed
     * 
     * @param event - reference to mouse event handler
     * @return selected card type based on mouse position
     */
    public static int getType(CGraphics g_src, int mouseX, int mouseY){
        int type=-1;
        for(int i=0;i<4;i++){
            if(getTypeOutline(g_src,i).contains(mouseX, mouseY))
                return i;
        }
        return -1;
    }
    
    /**
     * returns cursor depending on position on playfield
     * @param g_src - graphic content
     * @param player - player reference
     * @param mouseX - x position of mouse
     * @param mouseY - y position of mouse
     * @param active - true if playfield is active (you can choose card or deck)
     * @param activeTrash - true if trashfield is playable (in some games)
     * @param activeType - true if you choose type mode is active
     * @return 
     */
    public static Cursor getCursor(CGraphics g_src, CPlayer player, int mouseX, int mouseY,boolean active, boolean activeTrash, boolean activeType){
        if(activeType&&getType(g_src,mouseX,mouseY)>-1)
            return new Cursor(Cursor.HAND_CURSOR);
        int i=getCardIndex(player,g_src,mouseX,mouseY);
        if(active&&i>-1){
            if(activeTrash&&i==200)
                return new Cursor(Cursor.HAND_CURSOR); 
            if(i!=200)
                return new Cursor(Cursor.HAND_CURSOR); 
        }

        return new Cursor(Cursor.DEFAULT_CURSOR);
            
    }
    /*
     * Helper functions
     */
    
    private static Point getDeckPosition(CGraphics g_src, char type){
        Point result=new Point();
        
        int offset;
        if(Character.toLowerCase(type)=='d')
            offset=-3*g_src.cardWidth()/2;
        else
            offset=g_src.cardWidth()/2;
        
        result.x=g_src.displayWidth()/2+offset;
        result.y=g_src.displayHeight()/2-g_src.cardHeight()/2;
        
        return result;
    }
    
    private static Point getPlayerPosition(int player, CGraphics g_src, int numOfPlayers){
        int width=g_src.displayWidth();
        int height=g_src.displayHeight();
        
        if(player<1||player>numOfPlayers)
            return null;
        int [] positions=new int[numOfPlayers];
        int[][] pos=new int [5][2];
        pos[0][0]=width-80;
        pos[0][1]=height/2-45;
        
        pos[1][0]=5*width/6-50;
        pos[1][1]=height/6;
        
        pos[2][0]=width/2;
        pos[2][1]=50;
        
        pos[3][0]=width/6+50;
        pos[3][1]=pos[1][1];
        
        pos[4][0]=100;
        pos[4][1]=pos[0][1];
        
        switch(numOfPlayers)
        {
            case 1:
                positions[0]=2;
                break;
            case 2:
                positions[0]=1;
                positions[1]=3;
                break;
            case 3:
                positions[0]=0;
                positions[1]=2;
                positions[2]=4;
                break;
            case 4:
                positions[0]=0;
                positions[1]=1;
                positions[2]=3;
                positions[3]=4;
                break;
            default:
                for(int i=0;i<5;i++)
                {
                    positions[i]=i;
                }
        }
        
       return new Point(pos[positions[player-1]][0],pos[positions[player-1]][1]);
    }
    
    private static RoundRectangle2D.Double getTypeOutline(CGraphics g_src, int type){
        
        
        int width=g_src.displayWidth()/9;
        int y=g_src.displayHeight()/2-width/2;
        int x=width*(2*type+1);
        return new RoundRectangle2D.Double(x-5, y-5, width+10, width+10, 10, 10);
    }
    
    
}   
